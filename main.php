
<?php
require("pos.php");
require("neg.php");
require("negators.php");

/*
	takes the input and returns the verdict about it.
*/
function main($statement) {
	
	/*Cleaning the input data*/
	$statement = preg_replace('/\.+/','.',$statement);	
	$statement = preg_replace('/http:\/\/\S+/','',$statement);

	/*Calculate the result of the input statement*/
	$result = sentiment_calc($statement);
	return $result;
}

/*			
			Processes the statement and returns the verdict whether it is a Positive/Negative Response 
			or just a Neutral statement.
	
	*/
function sentiment_calc($statement) {
	/*statement is neutral initially.*/
	$pos_score = 0; 
	$neg_score = 0;
	$negator_score = 0;
	$verdict = '';
	/*If null statement then return*/
	if($statement == '' || $statement == ' ') return;
	/*If multiple statements, pass each statement*/
	if(strpos($statement, '.') !== false) {
		$sentences= explode('.', $statement);
		foreach ($sentences as $each_sentence) {
				if($each_verdict = sentiment_calc(trim($each_sentence)))
				$verdict .= $each_verdict.";";
		}
		/*calculate average sentiment and return*/
		return $verdict= $this->sentiment_avg($verdict);;
	}
	global $pos_array, $neg_array, $negator_array;
	
	if($statement != str_ireplace($negator_array, '', $statement))
			$negator_score++;
	if($statement != str_ireplace($pos_array, '', $statement))
			$pos_score++;
	if($statement != str_ireplace($neg_array, '', $statement))
			$neg_score++;
		
	
	/*If statement only contains negators then it is negative response.*/
	if($negator_score >0) {
		if($pos_score == 0 && $neg_score == 0) {
			return $verdict="NEGATIVE";
		}
		else {
			($pos_score > 0)?$pos_score = -1:'';
			($neg_score > 0)?$neg_score = -1:'';
		}
	}
	/*If statement has more postive words than its postive else negative*/
	($pos_score>$neg_score)?	$verdict="POSITIVE":$verdict="NEGATIVE";
	/*If statement has no positive/negative words then neutral*/
		if($pos_score===$neg_score) {$verdict="NEUTRAL";}

	return $verdict;
}

/*For multi-paragraph input, this function calculates the average sentiment*/
function sentiment_avg($verdict) {
	$verdict = explode(';', $verdict);
	$pos=0; $neg=0; $neutral=0;$result;
	foreach ($verdict as $sentiment) {
		switch ($sentiment) {
			case 'positive':
				$pos++;
				break;
			case 'negative':
				$neg++;
				break;
			case 'neutral':
				$neutral++;
				break;
			default:
				break;
		}
	}

	if($neutral > 0 && $pos == 0 && $neg == 0) {
		$result = 'neutral';
	}
	else if($pos > $neg) {
		$result = 'positive';
	}
	else if($neg > $pos) {
		$result = 'negative';
	}
	else if($neg == $pos) {
		$result = 'neutral';
	}

	return $result;
}

?>
