<?php
class Model_Helper_Diff{

	function readLCSFromBacktrack($backtrack, $string1, $string2, $s1position, $s2position){
	
		if($s1position > 0 && $s2position > 0 && ($string1[$s1position - 1] == $string2[$s2position - 1])){
			return (($this->readLCSFromBacktrack($backtrack, $string1, $string2, $s1position - 1, $s2position - 1)) . '' . ($string1[$s1position - 1]));
		}else{
			if($s2position > 0 && ($s1position == 0 || ($backtrack[$s1position][$s2position - 1] >= $backtrack[$s1position - 1][$s2position]))){
				$str1 = $string2[$s2position - 1][0] == '<' ? '' : 
					('<span style="background-color:#97FF97;">' . ($string2[$s2position - 1]) . '</span>');
				return $this->readLCSFromBacktrack($backtrack, $string1, $string2, $s1position, $s2position - 1) . $str1;
			}else if($s1position > 0 && ($s2position == 0 || ($backtrack[$s1position][$s2position - 1] < $backtrack[$s1position - 1][$s2position]))){
				$str2 = $string1[$s1position - 1][0] == '<' ? '' : 
					('<span style="color:#FF5F5F;text-decoration:line-through;">' . ($string1[$s1position - 1]) . '</span>');
				return $this->readLCSFromBacktrack($backtrack, $string1, $string2, $s1position - 1, $s2position) . $str2;
			}
		}
	}

	//ported from python.
	function longestCommonSubsequence($s1, $s2){
		$m = is_array($s1) ? sizeof($s1) + 1: strlen($s1) + 1;
		$n = is_array($s2) ? sizeof($s2) + 1: strlen($s2) + 1;
		$num = array();
		
		for($i = 0; $i < $m; $i++){
		   $num[$i][0] = 0;
		}
		for($j = 0; $j < $n; $j++){
			$num[0][$j] = 0;
		}
		
		for($i = 1; $i < $m; $i++){
			for($j = 1; $j < $n; $j++){
				/*if($s1[$i-1] == $s2[$j-1]){
					if((($i - 1) == 0) || (($j - 1) == 0)){
						$num[$i][$j] = 1;
					}else{
						$num[$i][$j] = 1 + $num[$i-1][$j-1];
					}

					//$num[$i][$j] = $num[$i-1][$j-1] + 1;
				}else{
					if((($i - 1) == 0) && (($j - 1) == 0)){
						$num[$i][$j] = 0;
					}else if((($i - 1) == 0) && !(($j - 1) == 0)){   //First ith element
						$num[$i][$j] = max(0, $num[$i][$j - 1]);
					}else if (!(($i - 1) == 0) && (($j - 1) == 0)){  //First jth element
						$num[$i][$j] = max($num[$i - 1][$j], 0);
					}else{ // if (!(i == 0) && !(j == 0))
						$num[$i][$j] = max($num[$i - 1][$j], $num[$i][$j - 1]);
					}
					//$num[$i][$j] = max($num[$i][$j-1], $num[$i-1][$j]);
				}*/
				if($s1[$i - 1] == $s2[$j - 1]){
					$num[$i][$j] = $num[$i-1][$j-1] + 1;
				}else{
					$num[$i][$j] = max($num[$i][$j-1], $num[$i-1][$j]);
				}

			}
		}
		return $num;
	}
	
	
	function printDiff($backtrack, $string1, $string2, $s1position, $s2position){
		if($s1position > 0 && $s2position > 0 && $string1[$s1position-1] == $string2[$s2position-1]){
			$this->printDiff($backtrack, $string1, $string2, $s1position-1, $s2position-1);
			echo "" . $string1[$s1position-1];
		}else{
			if($s2position > 0 && ($s1position == 0 || $backtrack[$s1position][$s2position-1] >= $backtrack[$s1position-1][$s2position])){
				$this->printDiff($backtrack, $string1, $string2, $s1position, $s2position-1);
				echo '<span style="background-color:#97FF97;">' . $string2[$s2position-1] . '</span>';
			}else if($s1position > 0 && ($s2position == 0 || $backtrack[$s1position][$s2position-1] < $backtrack[$s1position-1][$s2position])){
				$this->printDiff($backtrack, $string1, $string2, $s1position-1, $s2position);
				echo '<span style="color:#FF5F5F;text-decoration:line-through;">' . $string1[$s1position-1] . '</span>';
			}
		}
	}

	function splitHtml($htmlStr){

		$punct = '.,;:\')"!?-('; 				// punctuation
		$whiteSpace = " \n\t"; 					// white-space separators

		$status = 0;                			// init -- expecting char
		$curChar = '';                 			// current collection of chars
		$curWhiteSpace = '';					// current white space or empty prefix before the collected item
		$out = array();                  				// list of items
//		spc = []                  				// list of ws or empty prefixes before the item

		for($i = 0; $i < strlen($htmlStr); $i++){
			//echo $i . '<br/>';
			if($status == 0){              // collecting general chars, no space
				if($htmlStr[$i] == '<'){             // tag started
					if($curChar != ''){        // output the previous non-empty item
						//spc.append($curWhiteSpace)
						array_push($out, $curChar);
					}
					$curChar = $htmlStr[$i];              // the <
					$curWhiteSpace = '';
					$status = 1;           // switch to the tag collection status
				}else if(!(strrpos($punct, $htmlStr[$i]) === false)){           // punctuation started (often a single char)
					if($curChar != ''){        // output the previous non-empty item
						//spc.append($curWhiteSpace)
						array_push($out, $curChar);
					}
					//spc.append($curWhiteSpace)
					array_push($out, $htmlStr[$i]);        // output the punctuation char
					//assert $curWhiteSpace == ''
					$curChar = '';
				}else if(!(strrpos($whiteSpace, $htmlStr[$i]) === false)){            // space ended the previous item
					if($curChar != ''){        // output the previous non-empty item
						//spc.append($curWhiteSpace)
						array_push($out, $curChar);
					}
					$curWhiteSpace = $htmlStr[$i];               // white space of the kind
					array_push($out, $htmlStr[$i]);
					$curChar = '';             // this space will not be part of the item
					$status = 2;

				}else{
					$curChar .= $htmlStr[$i];             // collect other characters
				}

			}else if($status == 1){     // tag without preceeding space
				if($htmlStr[$i] == '>'){             // tag closed
					//spc.append($curWhiteSpace)
					array_push($out, ($curChar . $htmlStr[$i]));
					$curWhiteSpace = '';
					$curChar = '';
					$status = 0;           // char expected as next

				}else{
					$curChar .= $htmlStr[$i];             // collect the tag
				}

			}else if($status == 2){     // after first space
				if($htmlStr[$i] == '<'){             // tag started
					if($curChar != ''){
						//spc.append($curWhiteSpace)
						array_push($out, $curChar);
					}
					$curChar = $htmlStr[$i];
					$status = 3;           // tag after space collection mode

				}else if(!(strrpos($punct, $htmlStr[$i]) === false)){    // punctuation after space
					//spc.append($curWhiteSpace)
					array_push($out, $curChar);      // output even the empty, but space
					//spc.append('')
					array_push($out, $htmlStr[$i]);        // output the punctuation char, no space
					$curWhiteSpace = '';
					$curChar = '';
					$status = 0;

				}else if(!(strrpos($whiteSpace, $htmlStr[$i]) === false)){            // space ended the previous item
					if($curChar != ''){
						// output the previous non-empty item
						//spc.append($curWhiteSpace)
						array_push($out, $curChar);
					}
					$curWhiteSpace = $htmlStr[$i];               // white space of the kind
					array_push($out, $htmlStr[$i]);
					$curChar = '';             // this space will not be part of the item

				}else{
					$curChar .= $htmlStr[$i];
				}

			}else if($status == 3){     // tag after preceeding space
				if($htmlStr[$i] == '>'){             // tag closed
					//spc.append($curWhiteSpace)
					array_push($out,$curChar .$htmlStr[$i]);
					$curWhiteSpace = '';
					$curChar = '';
					$status = 0;           // char expected as next
				}else{
					$curChar .= $htmlStr[$i];             // collect the tag
				}
			}else{
				//assert False             // unimplemented status
			}
		}//end of for
		
		/*while($element = each($out)){
			$this->logger->debug("Comments:editComment() out : " . $element['key'] . " " . $element['value']);
		}*/
		
		if($curChar != ''){
			//assert $status==0
			//assert $status==1
			//spc.append($curWhiteSpace)
			array_push($out, $curChar);
		}
		return $out;
	}

}

?>
