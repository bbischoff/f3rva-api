<?php
namespace F3\Service;

use F3\Dao\ScraperDao;
use F3\Model\Member;
use F3\Model\Response;
use F3\Model\Workout;
use F3\Repo\Database;
use F3\Repo\WorkoutRepository;
use F3\Service\MemberService;
use F3\Util\DateUtil;

/**
 * Service class encapsulating business logic for workouts.
 * 
 * @author bbischoff
 */
class WorkoutService {
	private $memberService;
	private $scraperDao;
	private $workoutRepo;
	private $database;

	public function __construct(MemberService $memberService, ScraperDao $scraperDao, WorkoutRepository $workoutRepo, Database $database) {
		$this->memberService = $memberService;
		$this->scraperDao = $scraperDao;
		$this->workoutRepo = $workoutRepo;
		$this->database = $database;
	}

	/**
	 * Returns a Workout for a given workoutId
	 */
	public function getWorkout($workoutId) {
		$details = $this->workoutRepo->find($workoutId);
		$workoutObj = null;
		
		// Loop through all the workout results.  You may be thinking, why loop here?
		// This is to account for when there are workouts tagged with multiple Qs and/or AOs,
		// we will get dupes and need to collapse here.
		foreach ($details as $workout) {
			$workoutId = $workout['WORKOUT_ID'];
			if (is_null($workoutObj)) {
				$workoutObj = $this->createWorkoutObj($workout);

				// retrieve pax
				$paxList = $this->workoutRepo->findPax($workoutId);
				$paxArray = array();
				foreach ($paxList as $pax) {
					$member = new Member();
					$member->setMemberId($pax["MEMBER_ID"]);
					$member->setF3Name($pax["F3_NAME"]);
					$paxArray[$member->getMemberId()] = $member;
				}
				$workoutObj->setPax($paxArray);
			}
			else {
				// we already have the workout details, just add the duplicate info
				$workoutObj = $this->addAoToWorkout($workoutObj, $workout['AO_ID'], $workout['AO']);
				$workoutObj = $this->addQToWorkout($workoutObj, $workout['Q_ID'], $workout['Q']);
			}
		}
				
		return $workoutObj;
	}

	/**
	 * Retrieves all workouts from a point in time backwards $numberOfDaysBack
	 * 
	 * @return array of Workout
	 */
	public function getWorkouts($endDate = null, $numberOfDaysBack = null) {
		error_log('endDate: ' . $endDate);

		// set defaults
		if (is_null($endDate)) {
			$endDate = $this->workoutRepo->findMostRecentWorkoutDate();
		}
		if (is_null($numberOfDaysBack)) {
			$numberOfDaysBack = 5;
		}
		
		$startDate = DateUtil::subtractInterval($endDate, 'P' . $numberOfDaysBack . 'D');
		
		$workouts = $this->workoutRepo->findAllByDateRange($startDate, $endDate);
		
		return $this->processWorkoutResults($workouts);
	}
	
	/**
	 * Retrieves all workouts for a particular AO
	 * 
	 * @return array of Workout
	 */
	public function getWorkoutsByAo($aoId) {
		$workouts = $this->workoutRepo->findAllByAo($aoId);
		
		return $this->processWorkoutResults($workouts);
	}
	
	/**
	 * Retrieves all workouts for a particular Q
	 * 
	 * @return array of Workout
	 */
	public function getWorkoutsByQ($qId) {
		$workouts = $this->workoutRepo->findAllByQ($qId);
		
		return $this->processWorkoutResults($workouts);
	}
	
	/**
	 * Retrieves all workouts for a particular PAX member
	 * 
	 * @return array of Workout
	 */
	public function getWorkoutsByPax($paxId) {
		$workouts = $this->workoutRepo->findAllByPax($paxId);
		
		return $this->processWorkoutResults($workouts);
	}

	/**
	 * Parses a backblast from the source and returns the details on
	 * who led the workout, who attended, and any other details.
	 * 
	 * @return json encoded data of the backblast details
	 */
	public function parsePost($url) {
		return $this->scraperDao->parsePost($url);
	}

	/**
	 * Adds a new workout
	 * 
	 * @return \F3\Model\Response results of the attempt
	 */
	public function addWorkout($data) {
		// parse the post to get the information we need
		$additionalInfo = $this->scraperDao->parsePost($data->post->url);
		error_log('additionalInfo: ' . json_encode($additionalInfo));
		
		$response = new Response();

		// skip if it's a workout in the future
		if ($this->futureWorkout($additionalInfo)) {
			$response->setCode(Response::NOT_APPLICABLE);
		} 
		else {
			$db = $this->database->getDatabase();
		
			try {
				$db->beginTransaction();
				
				// insert the workout
				$response->setId($this->workoutRepo->save($additionalInfo->title, $additionalInfo->date, $data->post->url));
				
				// add the aos
				$this->saveWorkoutAos($response->getId(), $additionalInfo->tags);
				
				// add the qs
				$this->saveWorkoutQs($response->getId(), $additionalInfo->q);
				
				// add the pax members
				$this->saveWorkoutMembers($response->getId(), $additionalInfo->pax);
				
				$db->commit();
				$response->setCode(Response::SUCCESS);
			}
			catch (\Exception $e) {
				$db->rollBack();
				error_log('error adding workout, message: ' . $e->getMessage());
				$response->setCode(Response::FAILURE);
				$response->setId(null);
				$response->setMessage($e->getMessage());
			}
		}
		
		return $response;
	}
	
	public function refreshWorkout($workoutId) {
		$response = new Response();

		// get the workout
		$workout = $this->getWorkout($workoutId);
		if ($workout == null) {
			$response->setCode(Response::NOT_FOUND);
		}
		else {
			// parse the post to get the information we need
			$additionalInfo = $this->scraperDao->parsePost($workout->getBackblastUrl());
			error_log('additionalInfo: ' . json_encode($additionalInfo));
			
			// skip if it's a workout in the future
			if ($this->futureWorkout($additionalInfo)) {
				$response->setCode(Response::NOT_APPLICABLE);
			} 
			else {
				$db = $this->database->getDatabase();

				try {
					$db->beginTransaction();
					
					// update the workout
					$this->workoutRepo->update($workoutId, $workout->getTitle(), $additionalInfo->date, $workout->getBackblastUrl());
					
					// delete previous aos
					$this->workoutRepo->deleteWorkoutAos($workoutId);
					
					// add the aos
					$this->saveWorkoutAos($workoutId, $additionalInfo->tags);
					
					// delete the previous qs
					$this->workoutRepo->deleteWorkoutQs($workoutId);
					
					// add the qs
					$this->saveWorkoutQs($workoutId, $additionalInfo->q);
					
					// delete the previous members
					$this->workoutRepo->deleteWorkoutMembers($workoutId);
					
					// add the pax members
					$this->saveWorkoutMembers($workoutId, $additionalInfo->pax);
		
					$db->commit();
					$response->setCode(Response::SUCCESS);
					$response->setId($workoutId);
				}
				catch (\Exception $e) {
					$db->rollBack();
					error_log('error refreshing workout, message: ' . $e->getMessage());
					$response->setCode(Response::FAILURE);
					$response->setId(null);
					$response->setMessage($e->getMessage());
				} // catch
			} // future if
		} // found if
		
		return $response;
	}

	public function refreshWorkouts($numDays) {
		error_log('refreshing the past ' . $numDays . ' days');
		// get all workouts in the most recent days
		$workouts = $this->getWorkouts(DateUtil::getDefaultDate(null), $numDays);
		
		$response = new Response();
		$resultsArray = array();
		
		// loop through all workouts that meet criteria
		foreach ($workouts as $workout) {
			// refresh the workout
			$result = $this->refreshWorkout($workout->getWorkoutId());
			array_push($resultsArray, $result);

			if ($result->getCode() == Response::SUCCESS) {
				if (is_null($response->getCode()) || $response->getCode() == Response::SUCCESS) {
					$response->setCode(Response::SUCCESS);
				}
				else {
					$response->setCode(Response::PARTIAL);
				}
			}
			else {
				if ($response->getCode() == Response::SUCCESS) {
					$response->setCode(Response::PARTIAL);
				}
				else {
					$response->setCode(Response::FAILURE);
				}
			}
		}

		$response->setResults($resultsArray);
		
		return $response;
	}
	
	public function deleteWorkout($workoutId) {
		$success = false;
		$db = $this->database->getDatabase();

		try {
			$db->beginTransaction();
			
			// delete previous aos
			$this->workoutRepo->deleteWorkoutAos($workoutId);
			
			// delete the previous qs
			$this->workoutRepo->deleteWorkoutQs($workoutId);
			
			// delete the previous members
			$this->workoutRepo->deleteWorkoutMembers($workoutId);
			
			// delete the workout
			$this->workoutRepo->deleteWorkout($workoutId);
			
			$db->commit();
			$success = true;
		}
		catch (\Exception $e) {
			$db->rollBack();
			error_log('error deleting workout, message: ' . $e->getMessage());
		}
		
		return $success;
	}

	private function processWorkoutResults($workouts) {
		$workoutsArray = array();
		
		foreach ($workouts as $workout) {
			$workoutId = $workout['WORKOUT_ID'];
			
			if (!array_key_exists($workoutId, $workoutsArray)) { //is_null($workoutsArray[strval($workoutId)])) {
				$workoutObj = $this->createWorkoutObj($workout);
				$workoutsArray[$workoutObj->getWorkoutId()] = $workoutObj;
			}
			else {
				// we already have the workout details, just add the duplicate info
				if (!is_null($workout['AO_ID'])) {
					$existingWorkout = $workoutsArray[$workoutId];
					$existingWorkout = $this->addAoToWorkout($existingWorkout, $workout['AO_ID'], $workout['AO']);
				}
				if (!is_null($workout['Q_ID'])) {
					$existingWorkout = $workoutsArray[$workoutId];
					$existingWorkout = $this->addQToWorkout($existingWorkout, $workout['Q_ID'], $workout['Q']);
				}
			}
		}
		
		return $workoutsArray;
	}
	private function createWorkoutObj($workout) {
		$workoutObj = new Workout();
		
		$aoArray = array();
		// only add the AO if it exists
		if (!is_null($workout['AO_ID'])) {
			$aoArray[$workout['AO_ID']] = $workout['AO'];
		}
		$workoutObj->setAo($aoArray);
		
		$qArray = array();
		// only add the Q if it exists
		if (!is_null($workout['Q_ID'])) {
			$qArray[$workout['Q_ID']] = $workout['Q'];
		}
		$workoutObj->setQ($qArray);
		
		$workoutObj->setBackblastUrl($workout['BACKBLAST_URL']);
		$workoutObj->setPaxCount($workout['PAX_COUNT']);
		$workoutObj->setTitle($workout['TITLE']);
		$workoutObj->setWorkoutId($workout['WORKOUT_ID']);
		$workoutObj->setWorkoutDate($workout['WORKOUT_DATE']);
		
		return $workoutObj;
	}
	
	private function addAoToWorkout($workout, $aoId, $aoDescription) {
		$aoArray = $workout->getAo();
		
		if (!array_key_exists($aoId, $aoArray)) {
			$aoArray[$aoId] = $aoDescription;
			$workout->setAo($aoArray);
		}
		
		return $workout;
	}
	
	private function addQToWorkout($workout, $qId, $qName) {
		$qArray = $workout->getQ();
		if (!array_key_exists($qId, $qArray)) {
			$qArray[$qId] = $qName;
			$workout->setQ($qArray);
		}
		
		return $workout;
	}
	
	private function saveWorkoutAos($workoutId, $aos) {
		foreach ($aos as $ao) {
			$ao = $this->workoutRepo->selectOrAddAo($ao);
			$this->workoutRepo->saveWorkoutAo($workoutId, $ao->aoId);
		}
	}
	
	private function saveWorkoutMembers($workoutId, $pax) {
		foreach ($pax as $paxMember) {
			$member = $this->memberService->getOrAddMember($paxMember);
			$this->workoutRepo->saveWorkoutMember($workoutId, $member->getMemberId());
		}
	}
	
	private function saveWorkoutQs($workoutId, $qs) {
		foreach ($qs as $q) {
			$member = $this->memberService->getOrAddMember($q);
			$this->workoutRepo->saveWorkoutQ($workoutId, $member->getMemberId());
		}
	}
	
	private function futureWorkout($additionalInfo) {
		// check to see if this workout is in the future.  if it is then skip
		$dateArray = $additionalInfo->date;
		$dateStr = $dateArray['year'] . '-' . $dateArray['month'] . '-' . $dateArray['day'];
		if(strtotime(date('m/d/y', time())) < strtotime($dateStr)) {
			error_log('date is in the future');
			return true;
		}
		
		return false;
	}
}

?>
