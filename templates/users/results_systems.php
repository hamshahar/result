<?php
/** 
 * ### Function For Letter Grade
 * 
 * Create function {educare_letter_grade} for letter grade = A+, A, B, C, D, F (failed)
 * or points grade = 5, 4, 3.5, 3, 2, 1, 0 (based on default settings).
 * 
 * @since 1.0.0
 * @last-update 1.2.0
 * 
 * @param int $marks				Specific martks convert to grade or point
 * @param bull true/false 	For return grade points
 * 
 * @return string/int
 */

function educare_letter_grade($marks, $points = null) {
	/** 
	// ============== For Manually =================
	// check optional marks

	$optional_marks = substr(strstr($marks, ' '), 1);
	if ($optional_marks) {
		$marks = $optional_marks;
	}
	
	if ($marks >= 80 and $marks <= 100) {
			$marks = 'A+';
	}
	elseif ($marks >= 70 and $marks <= 79) {
			$marks = 'A';
	}
	elseif ($marks >= 60 and $marks <= 69) {
			$marks = 'A-';
	}
	elseif ($marks >= 50 and $marks <= 59) {
			$marks = 'B';
	}
	elseif ($marks >= 40 and $marks <= 49) {
			$marks = 'C';
	}
	elseif ($marks >= 33 and $marks <= 39) {
			$marks = 'D';
	} else {
		$marks = 'F';
	}
	*/

	$grade_system = educare_check_status('grade_system');
	$current = $grade_system->current;
	$grade_system = $grade_system->rules->$current;
	$grade_system = json_decode(json_encode($grade_system), true);

	/** 
	* Check optional subject marks
	* Note: Educare add 1 before optional subject marks.
	* Exp: 1 85
	* Her 1 = optional subject and 85 = marks
	* In this way educare define and identify optional subjects. So, when you add a result to the csv files - you need to add 1 symbol before the optional subject
	*/
	if (strpos($marks, ' ')) {
		$optional_marks = substr(strstr($marks, ' '), 1);
		$marks = $optional_marks;
	} else {
		$optional_marks = false;
	}
	
	// $optional_marks = substr(strstr($marks, ' '), 1);
	// if ($optional_marks) {
	// 	$marks = $optional_marks;
	// }

	foreach ($grade_system as $rules => $grade) {
		// if ($rules == 'failed' or $rules == 'success') break;
		// get first rules number to compare
		$rules1 = strtok($rules, '-');
		// get second rules number to compare
		$rules2 =substr(strstr($rules, '-'), 1);

		if ($marks >= $rules1 and $marks <= $rules2) {
			$marks = $grade[1];
		}
	}

	// return point grade if true
	if ($points) {
		foreach ($grade_system as $rules => $grade) {
			if ($marks == $grade[1]) {
				$marks = $grade[0];
			}
		}
		if ($optional_marks) {
			$marks = "1 $marks";
		}
	} else {
		// print success or failed
		$failed = end($grade_system);
		
		if ($marks == $failed[1]) {
			$marks = '<div class="failed">'.esc_html($marks).'</div>';
		} else {
			$marks = '<div class="success">'.esc_html($marks).'</div>';
		}
	}
	
	return $marks;
	
}



/**  
 * ### usage: educare_get_marks($print);
 * 
 * @since 1.0.0
 * @last-update 1.2.0
 * 
 * @param object $print	Print specific subject value
 * 
 * @return int
 */

function educare_get_marks($Subject) {
	$total_subject = 0;
	$points = array();

	foreach($Subject as $name => $mark) {
		$total_subject++;
		$points[] = educare_letter_grade($mark, true);
	}
	
	$add_optinal_mark = 0;
	$all_sub = $total_subject - 1;
	
	for ($x = 0; $x <= $all_sub; $x++) {
		$optinal = strtok($points[$x], ' ');
		$optinal = strtok($optinal);
		
		if ($optinal) {
			
			if ($optinal > 2) {
				$add_optinal_mark = $optinal - 2;
			} else {
				$add_optinal_mark = 0;
			}
		}
	}
	
	$main_subjects = array();
		foreach($points as $marks) {
			// detect optional subjects with [whtite-space]
			preg_match ('/ /', $marks, $optional); 
			if(count($optional) == 0) $main_subjects[] = $marks;
	}
	
	// $test = Array ( 0 => 3, 1=> 5, 2=> 4, 3=> 2 );
	
	/** 
	* You know, php array start in 0.
	* So, if your subject is 4 and we call $total_subject directly in loop expirations it's return 5 digit.
	* For example: 0, 1, 2, 3, 4. if you see loop start at 0 then 1, 2, 3..., our subject is 4 but it's looping 5 times. for solve this matter we need to decrease (4 - 1). here $total_subject = 4.
	# Notes: don't call $total_subject / --$total_subject directly in loop expirations. it's complicated the code!. first assign this a var. Let's do it-
	*/
	$count = count($main_subjects) - 1;
	
	$pass = true;
	for ($x = 0; $x <= $count; $x++) {
		$sub_points = $main_subjects[$x];
		if (empty ($sub_points)) {
			$pass = false;
		}
	}
	
	if ($pass == false) {
		$gpa = 0;
	} else {
		$gpa = array_sum($main_subjects);
		$gpa += $add_optinal_mark;
		if (count($main_subjects) >= 0) {
			$gpa /= count($main_subjects);
			// ignore unnecessary digits!
			$gpa = number_format((float)$gpa, 2, '.', '');
		}
	}

	$settings_rules = educare_check_status('grade_system');
	$current_rules = $settings_rules->current;
	$current_rules = $settings_rules->rules->$current_rules;
	$current_rules = json_decode(json_encode($current_rules), true);
	$max_rules = max($current_rules)[0];

	if ($gpa > $max_rules) {
		$gpa = number_format((float)$max_rules, 2, '.', '');
	}
	return $gpa;
}



/** 
 * ### display maarks/sanitize optional maarks
 * 
 * Usage: echo educare_display_marks(85);
 * 
 * @sincce 1.2.2
 * @last-update 1.2.2
 * 
 * @param object $marks 	init
 * 
 * @return init
 */

function educare_display_marks($marks) {

	if (strpos($marks, ' ')) {
		$marks = substr(strstr($marks, ' '), 1) . ' ' . educare_check_status('optional_sybmbol');
	}

	return $marks;
}



/** 
 * ### Create function for students result status
 * 
 * Usage: echo educare_results_status($subject, $id);
 * 
 * @sincce 1.0.0
 * @last-update 1.2.2
 * 
 * @param object $print 	Select specific subject
 * @param int $id					Specific subject id
 * @param int $gpa				return GPA if true, otherwise return passed/failed
 * 
 * @return string|HTML
 */

function educare_results_status($subject, $id, $gpa = null, $skip_html = null) {
	if ($skip_html) {
		$passed = true;
		$failed = false;
	} else {
		$passed = "<div class='success results_passed'>Passed</div>";
		$failed = "<div class='failed results_failed'>Failed</div>";
	}
	
	$geting_mark = educare_get_marks($subject);
	if ( !empty ($geting_mark)) {
		$status = $passed;
	} else {
		$status = $failed;
	}
	
	// Get auto results, if auto result is checked
	if (educare_check_status('auto_results') == 'checked') {
		if ($gpa) {
			$status = educare_get_marks($subject);
		} else {
			$status = $status;
		}
	} else {
		if ($gpa) {
			$getting_value = educare_value('GPA', $id);
			if (empty($getting_value)) {
				$status = '0';
			} else {
				$status = educare_value('GPA', $id);
			}
		} else {
			$status = strtolower(educare_value('Result', $id));
			if ($status == 'passed') {
				$status = $passed;
			} else {
				$status = $failed;
			}
		}
	}
	return $status;
}



/**
 * ### Front end educare results system
 * 
 * Usage:
 * in WordPress editor = [educare_results]
 * in PHP files = <?php echo do_shortcode( '[educare_results]' ); ?>
 * 
 * Futures:
 * 1. viewers can find result by Name, Registration number, Roll Number, Exam, Passing year.
 * 2. Results Table
 * 3. field validation
 * 4. Error Notice
 * 5. Profiles photos
 * 6. Print Results
 * etc...
 * 
 * @since 1.0.0
 * @last-update 1.4.1
 * 
 * @return mixed
 */

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) exit;

// Create shortcode fo Educare results
add_shortcode('educare_results', 'educare_results_shortcode' );

function educare_results_shortcode() {
	echo '<div id="educare-loading"><div class="educare-spinner"></div></div>';
	echo '<div id="educare-results-body" class="educare_results">';
	echo '<div id="msgs"></div>';
	educare_view_results();
	// #educare-results-body
	echo "</div>";
	?>

	<script>
		$(document).on("click", "#results_btn", function(event) {
			event.preventDefault();
			$(this).attr('disabled', true);
			var current = $(this);
			var form_data = $(this).parents('form').serialize();

			$.ajax({
				url: "<?php echo esc_url(admin_url('admin-ajax.php'))?>",
				data: {
					action: 'educare_proccess_view_results',
					form_data: form_data
				},
				type: 'POST',
				beforeSend: function(event) {
					$('#educare-loading').fadeIn();
				},
				success: function(data) {
					if (data.message) {
						var arr;

						if (data.message == 'Result not found. Please try again') {
							arr = 'success'
						} else {
							arr = 'error';
						}

						$('#msgs').html('<div class="results_form error_notice ' + arr + '">' + data.message) + '</div>';
					} else {
						$('#educare-results-body').html(data);
					}
					
				},
				error: function(data) {
					$('#educare-results-body').html(data + '<div class="notice notice-error is-dismissible"><p>Sorry, database connection error!</p></div>');
				},
				complete: function() {
					current.prop('disabled', false);
					$('#educare-loading').fadeOut();
					grecaptcha.reset();
				}
			});
		});
	</script>
	<?php
	
}


// Dont't close

