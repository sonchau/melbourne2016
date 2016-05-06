<?php include($_SERVER['DOCUMENT_ROOT'] . '/includes/_cMail.php');?>
<?php include($_SERVER['DOCUMENT_ROOT'] . '/includes/_cSms.php');?>
<?php include($_SERVER['DOCUMENT_ROOT'] . '/includes/_cFee.php');?>



	<?php

		class Person {


			var $FullName        = "";

			var $Age             = "";

			var $AirportTransfer = false ;

			var $Airbed          = false ;

			var $FamilyDiscount  = "";

			var $Relation        = "";

			var $Fee			 = 0;

			var $Gender 		 = "";

			var $Role 			 = "";

			var $Cancelled		 = false;




			//new
			function Person() { }


				function isValid(){

					//Name and Age is required and age must be a number
					return !($this->FullName == "" || $this->Age == "" || is_numeric($this->Age) == false) ;

				}


				function validateFamilyDiscount(){

					//if the selection is 2nd child 5yo and under, must be less than 6yo and must be son/daughter
					if ($this->FamilyDiscount == '2nd child 5yo and under'){
						$num = (int)$this->Age;
						if ($this->Relation == 'Son' || $this->Relation == 'Daughter'){
							return ($num < 6);
						}else{
							return false;
						}		
					}
					
					return true;

				}


		}







		// base class with member properties and methods
		class Registration {

			var $MainContactId   = -1;

			var $FullName        = "";

			var $Age             = 0;

			var $Church          = "";

			var $Email           = ""       ;

			var $Phone           = "" ;

			var $DateTimeEntered = "";

			var $AirportTransfer = false ;

			var $Airbed          = false ;

			var $Comments        = "" ; 

			var $JSON            = "";

			var $Fee			 = 0;

			var $PersonStack     = array(); //array of persons object

			var $debug			 = false;

			var $errMsg			 = "";

			var $Reference		 = "";

			var $Gender 		 = "";

			var $Role 			 = "";

			var $Cancelled		 = false;




			const SQL_DB_NAME 		= 'melbou99_mysql';

			const SQL_DB_USERNAME 	= 'melbou99_mysql';

			const SQL_DB_PASSWORD 	= 'daihoi2016!';




			/*
			// constructor
			*/
			function Registration($json) {

				$this->JSON = $json;

				date_default_timezone_set('Australia/Melbourne');

				$this->DateTimeEntered = date("Y/m/d H:i:s");

			}



			/*
			// logs an error
			*/
			function logError($err){

				$this->errMsg .= $err . "\n";

				if ($this->debug){

					echo $err;

				}



			}







			/*
			// validates the entire fee structure
			// this function used to check the integrity of the inputted fees against the fee structure.
			*/
			function validateFees(){

				$fee = $this->calculateFee($this->Age, '-', $this->Airbed , $this->AirportTransfer );

				if ($fee !== $this->Fee){

					$this->logError('Main Fee expected is ' . $fee . ', but recieved is ' . $this->Fee . '.');

					return false;

				}


					foreach ($this->PersonStack as $member) {


							if ($member->isValid()) { //validates name, age and numeric age

								$fee = $this->calculateFee($member->Age, $member->FamilyDiscount, $member->Airbed , $member->AirportTransfer );

								if ($fee !== $member->Fee){ //validates fee

									$this->logError('Member ('. $member->FullName . ') Fee expected is ' . $fee . ', but recieved is ' . $member->Fee . '.');

									return false;

								}

							}	

					}

				return true;

			}











			function isValid(){ //checks if object is valid, usally after parseJSON

				//Name and Age is required and age must be a number
				$res = !($this->FullName == "" || $this->Age == "" || is_numeric($this->Age) == false || $this->Email == "" || $this->Phone == "");

				if ($res == false){

					$this->logError("Main contact failed integrity check.");

					return false;

				}




				//check all members are valid

				foreach ($this->PersonStack as $member) {

					if ($member->isValid() == false) {

						$this->logError("Members failed integrity check.");

						return false;

					}else{

						//validates any family discount
						if ($member->validateFamilyDiscount() ==  false){

							$this->logError('Member ('. $member->FullName . ') FamilyDiscount (' . $member->FamilyDiscount . ') must be 5yo or under and Relation must be Son or Daughter.'  );

							return false;

						}

					}

				}				



				//check the fee

				if ($this->validateFees() == false){

 					$this->logError("Fees failed integrity check.");

					return false;

				}

				return true;

			}











			/*
			//	calculates a fee 
			*/
			function calculateFee(	$Age = 0, 
									$FamilyDiscount = '-', 
									$Airbed = 0, 
									$AirportTransfer = 0){


				//create a calculator objcet
				$calculator = new FeeCalculator();

				//use object to calculate fee
            	return $calculator->calculateFee($Age, $FamilyDiscount, $Airbed, $AirportTransfer);

			}


			/*
			// will convert json data to object
			*/
			function parseJSON(){

					if ($this->JSON == ""){
						return false;
					}


					$rego = json_decode($this->JSON,true);


					//echo $rego['Name'];
					//echo $group['Registrants'][0]['Name'];

					//assign the property of the object from the json array
					$this->FullName        = trim($rego['Name']);

					$this->Age             = $rego['Age'];

					$this->Church          = trim($rego['Church']);

					$this->Email           = trim($rego['Email']);

					$this->Phone           = trim($rego['Phone']);

					$this->Airbed          = false; //$rego['Airbed'];

					$this->AirportTransfer = $rego['AirportTransfer'];

					$this->Fee             = $rego['Fee'];

					$this->Comments        = trim($rego['Comments']);

					$this->Gender          = $rego['Gender'];

					$this->Role            = $rego['Role'];




					foreach ($rego['Registrants'] as $member) {



						//create the person object
						$newPerson = new Person();
	
						//assign property to person object from json array

						$newPerson->FullName        = trim($member['Name']);

						$newPerson->Age             = $member['Age'];

						$newPerson->Relation        = $member['Relation'];

						$newPerson->FamilyDiscount  = $member['DiscountFamily'];

						$newPerson->Airbed          = false; //$member['Airbed'];

						$newPerson->AirportTransfer = $member['AirportTransfer'];

						$newPerson->Fee             = $member['Fee'];

						$newPerson->Gender          = $member['Gender'];

						$newPerson->Role            = $member['Role'];



						if ($newPerson->isValid()) {

							//add to array if valid
							array_push($this->PersonStack, $newPerson);

						}

					}


					return true;

			}






			function test_input($data) {
			  $data = trim($data);

			  $data = stripslashes($data);

			  $data = htmlspecialchars($data);

			  return $data;

			}




			function toString(){

				echo "\n: Methods\n";

				$arr = get_class_methods(get_class($this));

				foreach ($arr as $method) {

					    echo "\tfunction $method()\n";

				}

				echo "\n: propoerties\n";

				foreach (get_object_vars($this) as $prop => $val) {

				    echo "\t$prop = $val\n";

				}



			}



			function getClientIP(){


				if (!empty($_SERVER['HTTP_CLIENT_IP'])) {

				    $ip = $_SERVER['HTTP_CLIENT_IP'];

				} elseif (!empty($_SERVER['HTTP_X_FORWARDED_FOR'])) {

				    $ip = $_SERVER['HTTP_X_FORWARDED_FOR'];

				} else {

				    $ip = $_SERVER['REMOTE_ADDR'];

				}

				return $ip;

			}			







			function generateRandomString($includeGUI = false)	{



			    $characters = '0123456789ABCDEFGHIJKLMNOPQRSTUVWXYZ';

			    $charactersLength = strlen($characters);

			    $randomString = '';

				$length = ($includeGUI) ? 5 : 10 ;


			    for ($i = 0; $i < $length; $i++) {

			        $randomString .= $characters[rand(0, $charactersLength - 1)];

			    }



			    if ($includeGUI){

			    	$randomString = $randomString . "/" . substr(strtoupper(uniqid()),5);

			    }


			    //we try to ensure the reference is unique

			    //require '../db-admin/_db.php';

				//$database = createDb();

			    return $randomString;

			}			





			/*
			// adds the current object to the database
			*/
			function commitDB(){


				$myJSON = $this->JSON;


				if ($myJSON == ""){

					$this->logError("No json data.");

					return false;

				}



				$mysqli = new mysqli("localhost", self::SQL_DB_USERNAME, self::SQL_DB_PASSWORD, self::SQL_DB_NAME);

				if ($mysqli->connect_errno) {

				    $this->logError("Failed to connect to MySQL: (" . $mysqli->connect_errno . ") " . $mysqli->connect_error);

				}
				//echo $mysqli->host_info . "\n";





				/* change character set to utf8 */
				if (!$mysqli->set_charset("utf8")) {

				    $this->logError("Error loading character set utf8:" . $mysqli->error);

				    exit();

				} else {

				    //printf("Current character set: %s\n", $mysqli->character_set_name());

				}







				//sets the timezone for DateTime fields with CURRENT_TIMESTAMP as defaults

				$mysqli->query("SET time_zone = '+10:00'");





				//we isnert json into log
				/* Prepared statement, stage 1: prepare */

				if (!($stmt = $mysqli->prepare("INSERT INTO DataLog (jsonData, IP, Reference, ClientBrowser) VALUES ( ?, ?, ?, ?)"))) {

				    $this->logError("Prepare failed for DataLog: (" . $mysqli->errno . ") " . $mysqli->error);

				}				







				$this->Reference = $this->generateRandomString(true);

				$Reference = $this->Reference;

				$ip = $this->getClientIP();

				$ClientBrowser = $_SERVER['HTTP_USER_AGENT'] ; //. get_browser();


				if (!$stmt->bind_param("ssss", $myJSON   , $ip , $Reference, $ClientBrowser)) {

				    $this->logError("Binding parameters failed for DataLog: (" . $stmt->errno . ")<br />  " . $stmt->error);

				}



				if (!$stmt->execute()) {

				    $this->logError("Execute failed DataLog: (" . $stmt->errno . ")<br /> " . $stmt->error);

				}



				/* Prepared statement, stage 1: prepare */

				if (!($stmt = $mysqli->prepare("INSERT INTO MainContact (FullName, Age, Church, Email, Phone, DateTimeEntered, AirportTransfer, Airbed, Comments, Fee, Reference, Role, Gender) VALUES ( ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?);"))) {

				    $this->logError("Prepare failed MainContact: (" . $mysqli->errno . ") " . $mysqli->error);

				}







				/* Prepared statement, stage 2: bind and execute */
				$FullName        = $this->FullName;

				$Age             = $this->Age;

				$Church          = $this->Church;

				$Email           = $this->Email;

				$Phone			 = $this->Phone;

				$DateTimeEntered = $this->DateTimeEntered;

				$AirportTransfer = $this->AirportTransfer;

				$Airbed          = $this->Airbed; 

				$Comments        = $this->Comments;

				$Fee             = $this->Fee;

				$Role 			 = $this->Role;

				$Gender 		 = $this->Gender;




				if (!$stmt->bind_param("sssssssssssss", $FullName, 

															$Age, 

															$Church, 

															$Email, 

															$Phone, 

															$DateTimeEntered, 

															$AirportTransfer, 

															$Airbed, 

															$Comments, 

															$Fee,

															$Reference,

															$Role,

															$Gender)) {

														

				    $this->logError( "Binding parameters failed MainContact: (" . $stmt->errno . ")<br />  " . $stmt->error);

					return false;

				}







				if (!$stmt->execute()) {

				    $this->logError("Execute failed MainContact: (" . $stmt->errno . ")<br /> " . $stmt->error);

				    return false;
				}





				$newMainContactId = mysqli_insert_id($mysqli);
	
				//update the status
				$status = 1;

				if (!($stmt = $mysqli->prepare("UPDATE DataLog SET  Status = ? WHERE jsonData = ? AND IP = ? AND Reference = ?"))) {

				    $this->logError("Prepare failed for DataLog Update 1: (" . $mysqli->errno . ") " . $mysqli->error);

				}				



				if (!$stmt->bind_param("ssss", $status , $myJSON, $ip , $Reference)) {

				    $this->logError("Binding parameters failed for DataLog Update 1: (" . $stmt->errno . ")<br />  " . $stmt->error);

				}




				if (!$stmt->execute()) {

				    $this->logError("Execute failed DataLog Update 1: (" . $stmt->errno . ")<br /> " . $stmt->error);

				}



				//end update status


				$len = count($this->PersonStack) ;

				if ( $len > 0) {



					$membersUpdateError = 0;


					//insert any members in group
 					/* Prepared statement, stage 1: prepare */
					if (!($stmt = $mysqli->prepare("INSERT INTO Registrant (MainContactId, FullName, Age, Relation, FamilyDiscount, Airbed, AirportTransfer,Fee, Role, Gender) VALUES ( ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)"))) {

					    $this->logError("Prepare failed members: (" . $mysqli->errno . ") " . $mysqli->error);

					    $membersUpdateError =1;



					}				



					if (!$stmt->bind_param("ssssssssss", $newMainContactId, 

														$FullName, 

														$Age, 

														$Relation,

														$FamilyDiscount,

														$Airbed, 

														$AirportTransfer, 

														$Fee,

														$Role, 

														$Gender)) {



					    $this->logError("Binding parameters failed members: (" . $stmt->errno . ")<br />  " . $stmt->error);

						$membersUpdateError =1;



					}







					foreach ($this->PersonStack as $member) {


								if ($member->isValid()) {

						
									$FullName        = $member->FullName;

									$Age             = $member->Age;

									$Relation        = $member->Relation;

									$FamilyDiscount  = $member->FamilyDiscount;

									$AirportTransfer = $member->AirportTransfer;

									$Airbed          = $member->Airbed; 

									$Fee             = $member->Fee; 

									$Role            = $member->Role; 

									$Gender          = $member->Gender; 




									if (!$stmt->execute()) {

									    $this->logError("Execute failed members: (" . $stmt->errno . ")<br /> " . $stmt->error);

									    $membersUpdateError =1;


									}else{

										//echo "<br />Records created for person" . $FullName . "...<hr />"	;		

									}

								}	



					}



					//if theres an error we update status to 2, if no error then we update to 3
					$status = ($membersUpdateError == 1) ? 2 : 3 ;




					//update the status
					if (!($stmt = $mysqli->prepare("UPDATE DataLog SET  Status = ? WHERE jsonData = ? AND IP = ? AND Reference = ?"))) {

					    $this->logError("Prepare failed for DataLog Update 2: (" . $mysqli->errno . ") " . $mysqli->error);

					}				


					if (!$stmt->bind_param("ssss", $status , $myJSON, $ip , $Reference)) {

					    $this->logError("Binding parameters failed for DataLog Update 2: (" . $stmt->errno . ")<br />  " . $stmt->error);

					}




					if (!$stmt->execute()) {

					    $this->logError("Execute failed DataLog Update 2: (" . $stmt->errno . ")<br /> " . $stmt->error);

					}


					//end update status

				}


				$mysqli->close();


				return true;


			}




			function ToYesNo($v){

				return ($v == "1" || $v == true || $v == 1) ? "Yes" : "No" ; 

			}



			/*
			// fetches registration from database
			*/
			function getRego($ref){

					$mysqli = new mysqli("localhost", self::SQL_DB_USERNAME, self::SQL_DB_PASSWORD, self::SQL_DB_NAME);

					if ($mysqli->connect_errno) {

					    echo "Failed to connect to MySQL: (" . $mysqli->connect_errno . ") " . $mysqli->connect_error;
					}



					//echo $mysqli->host_info . "\n";
					//echo "<hr />";
					/* change character set to utf8 */


					if (!$mysqli->set_charset("utf8")) {
					    $this->logError("Error loading character set utf8:" . $mysqli->error);
					    exit();
					} else {

					    //printf("Current character set: %s\n", $mysqli->character_set_name());

					}



					$output = "";


					$paymentMsgSuccess = '

					    <div class="col-lg-12"> 
					        <div class="panel panel-success">
					          <div class="panel-heading">
					            <h3 class="panel-title">PAYMENT COMPLETED</h3>
					          </div>
					          <div class="panel-body" style="color:#000;">

					                <i class="fa fa-check fa-5x pull-left text-success"> </i> Good news! We are happy to inform you that the registration payment of <span class="text-success">$X</span> has been received and confirmed. We thank you for your support and look forward to seeing you at the conference.    

					          </div> 
					        </div>
					    </div>



					';





					$paymentMsgInfo = '
                        <div class="col-lg-12">
                            <div class="panel panel-info">
                              <div class="panel-heading">
                                <h3 class="panel-title">PAYMENT PROGRESS</h3>
                              </div>
                              <div class="panel-body" style="color:#000;">

                                    <i class="fa fa-star-half-o fa-5x pull-left text-info"> </i> Thank you for the registration payment of <span class="text-success">$X</span>, it has been received and confirmed. However, there is the balance of <span class="text-warning">$Y</span> still outstanding. Please ensure the amount is paid in full as soon as possible. 

                                    We are happy to discuss any payment arrangements, you can contact us via the links above.

                              </div>

                            </div>

                        </div>

					';



					// selects

					if ($res = $mysqli->query("SELECT C.*, R.FullName RName, R.Age RAge, R.Relation RRelation, R.FamilyDiscount RFamilyDiscount, R.Airbed RAirBed, R.AirportTransfer RAirportTransfer, R.Fee RFee, R.Gender RGender, R.Role RRole, R.Cancelled RCancelled, IFNULL((SELECT SUM(P.PaidAmount) FROM Payment P WHERE P.MainContactId = C.MainContactId),0) TotalPaid FROM MainContact C LEFT OUTER JOIN Registrant R ON R.MainContactId=C.MainContactId WHERE C.Reference = '" . $ref . "';")){



							if ($res->num_rows > 0) {



									//the row templates

				        			$row1 = "<tr><td>%s</td><td>%s</td><td>%s</td><td>%s</td><td>%s</td><td>%s</td><td>%s</td><td>$%d</td></tr>";

				        			$row2 = '<tr><td>%s.</td><td>%s</td><td>%s</td><td>$%s</td></tr>';


									$rowCancelled1  = '<tr class="strikeout"><td>%s</td><td>%s</td><td>%s</td><td>%s</td><td>%s</td><td>%s</td><td>%s</td><td>$%d</td></tr>';
									$rowCancelled2  = '<tr class="strikeout"><td>%s.</td><td>%s</td><td>%s</td><td>$%s</td></tr>';


									$memberRowHtml = "";

									$paymentRowHtml = "";



									$res->data_seek(0);

									$counter = 0;

									$membersFeeTotal = 0;

									$PaidAmountTotal = 0;



									while ($row = $res->fetch_assoc()) {


											$counter = $counter + 1;


											if($counter == 1){

												//first row
												//assign the property of the object from the json array



												$this->FullName        = $row['FullName'];

												$this->Age             = $row['Age'];

												$this->Church          = $row['Church'];

												$this->Email           = $row['Email'];

												$this->Phone           = $row['Phone'];

												$this->Airbed          = $row['Airbed'];

												$this->AirportTransfer = $row['AirportTransfer'];

												$this->Fee             = $row['Fee'];

												$this->Comments        = $row['Comments'];

												$this->Reference       = $row['Reference'];

												$this->Gender          = $row['Gender'];

												$this->Role       	   = $row['Role'];

												$this->Cancelled	   = $row['Cancelled'];




												//add to payment summary row

												$paymentRowHtml = sprintf(

													($this->Cancelled) ? $rowCancelled2 : $row2 

													, $counter , $this->FullName, $this->Age, $this->Fee);



												//get the total paid
												$PaidAmountTotal = $row['TotalPaid'];

											}



											//create the person object
											$newPerson = new Person();



											//assign property to person object from json array
											$newPerson->FullName        = $row['RName'];

											$newPerson->Age             = $row['RAge'];

											$newPerson->Relation        = $row['RRelation'];

											$newPerson->FamilyDiscount  = $row['RFamilyDiscount'];

											$newPerson->Airbed          = $row['RAirBed'];

											$newPerson->AirportTransfer = $row['RAirportTransfer'];

											$newPerson->Fee             = $row['RFee'];

											$newPerson->Gender          = $row['RGender'];

											$newPerson->Role            = $row['RRole'];

											$newPerson->Cancelled       = $row['RCancelled'];


											//totals the members (not main contact)

											if ($newPerson->Cancelled == false){

												$membersFeeTotal = $membersFeeTotal + $newPerson->Fee;

											}


											//add to registrant row

											if ($newPerson->isValid()){

												$memberRowHtml .= sprintf(
													($newPerson->Cancelled) ? $rowCancelled1 : $row1
													, 


																			$newPerson->FullName, 

																			$newPerson->Age, 

																			$newPerson->Gender, 

																			$newPerson->Relation, 

																			$newPerson->FamilyDiscount, 

																			$this->ToYesNo($newPerson->AirportTransfer),  

																			$newPerson->Role, 

																			$newPerson->Fee, null); //$this->ToYesNo($newPerson->Airbed),  



												//add to payment summary row



												$paymentRowHtml .= sprintf(

													($newPerson->Cancelled) ? $rowCancelled2 : $row2

													, ($counter + 1) , $newPerson->FullName, $newPerson->Age, $newPerson->Fee);



												//add to array

												array_push($this->PersonStack, $newPerson);													


											}


									}		









									//finds template and formats to php formatted string

									$html = file_get_contents($_SERVER['DOCUMENT_ROOT'] . "/register/summary_template.php");


									for ($i=0; $i < 17 ;  $i++) { 

										$html = str_replace( "{" . $i . "}", "%s", $html);

									}



									//replace the js functions

									$html  = str_replace("SUBMISSION.submitRegistration(this)","window.print();",$html);

									$html  = str_replace("REGISTER ME !"," PRINT ",$html);

									$html  = str_replace("swapRegoSummary","return false;void",$html);

									$html  = str_replace("{REFERENCE}",$ref,$html);







									//user payment logic



									$totalPayableAmount = $membersFeeTotal;

									if ($this->Cancelled == false){

										$totalPayableAmount = $totalPayableAmount + $this->Fee;

									}







									//if paid in full



									if ($PaidAmountTotal == $totalPayableAmount){

										setlocale(LC_MONETARY, 'en_AU'); 

										$paymentMsgSuccess = str_replace('$X', money_format('%#0n', $PaidAmountTotal), $paymentMsgSuccess);

										$html              = str_replace("<!--PAYMENT-PROGRESS-->", $paymentMsgSuccess, $html);



									}elseif ($PaidAmountTotal > 0 && $PaidAmountTotal < $totalPayableAmount){

										setlocale(LC_MONETARY, 'en_AU'); 

										$paymentMsgInfo = str_replace('$X', money_format('%#0n', $PaidAmountTotal), $paymentMsgInfo);

										$paymentMsgInfo = str_replace('$Y', money_format('%#0n', ($totalPayableAmount - $PaidAmountTotal)), $paymentMsgInfo);

										$html           = str_replace("<!--PAYMENT-PROGRESS-->", $paymentMsgInfo, $html);


									}





									$html  = str_replace('<?php include($_SERVER["DOCUMENT_ROOT"] . "/includes/_bankdetails.php");?>',
										file_get_contents($_SERVER['DOCUMENT_ROOT'] . "/includes/_bankdetails.php")

									,$html);




									$output = sprintf($html, 

										'<span class="pull-right">Reference: <span class="label label-success label-summary-total">' . $this->Reference . '</span></span>', 

										$this->FullName, 

										$this->Age, 

										$this->Email, 

										$this->Phone,

										$this->Role, 

										$this->Gender,	//$this->ToYesNo($this->Airbed), 

										$this->ToYesNo($this->AirportTransfer),

										$this->Church, 

										$this->Fee,

										$this->Comments,

										((count($this->PersonStack)) >= 1) ? 'X1' : 'hidden', //hidden if no members

										$memberRowHtml,

										$membersFeeTotal,

										$paymentRowHtml,

										$totalPayableAmount,

										null

										);



							}else{

								//no records

								$output = '<div class="alert alert-dismissible alert-danger"><button type="button" class="close" data-dismiss="alert">X</button><i class="fa fa-frown-o fa-5x pull-left"> </i><strong>Oh no!</strong> We could not find any records the given reference number. You can try to <a href="/contact/" class="alert-link">contact the conference team</a>. for help concerning your registration.<div class="clearfix"></div></div>';



							}





						/* free result set */

    					$res->close();



					}


				$mysqli->close();


				return $output;



			}




			/*
			//	updates the SMS message Id to the data log
			*/

			function updateSMSMessageId($ref, $messageId){

					$mysqli = new mysqli("localhost", self::SQL_DB_USERNAME, self::SQL_DB_PASSWORD, self::SQL_DB_NAME);

					if ($mysqli->connect_errno) {

					    $this->logError("Failed to connect to MySQL: (" . $mysqli->connect_errno . ") " . $mysqli->connect_error);

					}


					/* change character set to utf8 */
					if (!$mysqli->set_charset("utf8")) {

					    $this->logError("Error loading character set utf8:" . $mysqli->error);

					    exit();

					} else {

					    //printf("Current character set: %s\n", $mysqli->character_set_name());

					}



					//we isnert json into log
					/* Prepared statement, stage 1: prepare */

					if (!($stmt = $mysqli->prepare("UPDATE DataLog SET messageId = ? WHERE Reference = ? AND (messageId = '' OR messageId IS NULL);" ))) {

					    $this->logError("Prepare failed for DataLog: (" . $mysqli->errno . ") " . $mysqli->error);

					}				



					if (!$stmt->bind_param("ss",  $messageId, $ref )) {

					    $this->logError("Binding parameters failed for DataLog: (" . $stmt->errno . ")<br />  " . $stmt->error);

					}



					if (!$stmt->execute()) {

					    $this->logError("Execute failed DataLog: (" . $stmt->errno . ")<br /> " . $stmt->error);

					}

					$mysqli->close();

			}







			/*
			//	checks if rego exists based on json 
			*/
			function exists(){

					$json = $this->JSON;

					$return = false;


					$mysqli = new mysqli("localhost", self::SQL_DB_USERNAME, self::SQL_DB_PASSWORD, self::SQL_DB_NAME);

					if ($mysqli->connect_errno) {			

			     		$this->logError("Failed to connect to MySQL: (" . $mysqli->connect_errno . ") " . $mysqli->connect_error);

					}




					/* change character set to utf8 */

					if (!$mysqli->set_charset("utf8")) {

					    $this->logError("Error loading character set utf8:" . $mysqli->error);

					    exit();

					} 



					if (!($stmt = $mysqli->prepare("SELECT * FROM DataLog WHERE jsonData = ? AND Status > 0"))) {

					    $this->logError("Prepare failed for DataLog Update query: (" . $mysqli->errno . ") " . $mysqli->error);

					}				


					if (!$stmt->bind_param("s", $json)) {

					    $this->logError("Binding parameters failed for DataLog Update 2query (" . $stmt->errno . ")<br />  " . $stmt->error);

					}


					if (!$stmt->execute()) {

					    $this->logError("Execute failed DataLog Update query: (" . $stmt->errno . ")<br /> " . $stmt->error);

					}


					if ($stmt->fetch() > 0) {

						$return = true;

					}



					$mysqli->close();


					return $return;


			}

		} // end of class rego





		class OUTPUTj { //class to hold output to json

			var $status    = 0;
			var $reference = "";
			var $message   = "";

			function OUTPUTj($s, $r, $m){
				$this->status    = $s;
				$this->reference = $r;
				$this->message   = $m;
			}


			function toJSON(){
				return json_encode($this);

			}

		} // endclass to hold output to json









		function viewRego(){


			if ($_GET["ref"] ){

				$rego = new Registration("");

				echo $rego->getRego(trim(html_entity_decode($_GET["ref"])));

			}else{



					echo '
							<form role="form" class="horizontal-form" action="." method="get">
                                <div class="form-group">
                                    <label class="col-md-2 control-label">Registration Reference</label>
                                    <div class="col-md-6 control-label">
                                        <input type="text" maxlength="20" name="ref" class="form-control" />
                                    </div>
                                    <div class="col-md-4">
                                    	<input type="submit" class="btn btn-default" value=" Lookup Registration " />
                                    </div>
                                </div>    
                            </form>
                            <div style="margin-bottom: 200px;">
                            	&nbsp;
                            </div>
						';

			}



		}



		function processRegoSubmission(){



				$json = $_POST["json"];

				if( $json || $_POST["reference"] ) {


			      	if ($json<> ""){

			      		//create new object with json data
						$rego = new Registration($json);


						//check if json data exists

						if ($rego->exists()) {

							$out = new OUTPUTj(0,"","This registration information already exists!");

							echo $out->toJSON();

							return false;


						}




						//json to objects

						$rego->parseJSON(); 


						//make sure the json converted is valid

						if ($rego->isValid() == false){ 

							$out = new OUTPUTj(0,"",$rego->errMsg); 

							echo $out->toJSON();

							return false;

						}						




						//$rego->toString();

						if ($rego->commitDB()){
				
							$ref = $rego->Reference;


							//send sms


							try {

								//we try this as we dont want to show error if sms fails
								//we still want to show the registration information
								//check for aussie mobile prefix



								if ( substr($rego->Phone,0,5) == "+6104" || substr($rego->Phone,0,4) == "+614") {

							        $sms = new SMS();

							        if($sms->access_token){


							            $messageId =  $sms->send($rego->Phone, 

							            	'Hi ' . $rego->FullName . ', your ref is ' . $ref  .'. View your info at http://goo.gl/asxolc.\n\nDaiHoi Melbourne2016 Team.'); 

							            if($messageId){

							            	$rego->updateSMSMessageId($rego->Reference, $messageId);


							            }

							        }									


								}



							} catch (Exception $e) {

								//should log error in db

							}


							//we send email

							try {

								//we try this as we dont want to show error if email fails
								//we still want to show the registration information


								$message = $rego->getRego($ref);
								$email = new Mailer();
								$email->sendMail($rego->Email, 'DaiHoi 2016 Registration [' . $ref . '] for: ' . $rego->FullName , $message);



							} catch (Exception $e) {


								//should log error in db

							}



							$out = new OUTPUTj(1,$ref,$rego->errMsg);
							echo $out->toJSON();



						}else{

					
							$out = new OUTPUTj(0,"",$rego->errMsg);

							echo $out->toJSON();


						}


					}

			   }

		}




	?>


