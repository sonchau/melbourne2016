	<?php
		class Person {
			var $FullName        = "";
			var $Age             = "";
			var $AirportTransfer = false ;
			var $Airbed          = false ;
			var $FamilyDiscount  = "";
			var $Relation        = "";
			var $Fee			 = 0;


			//new
			function Person() { }

			function isValid(){
				//Name and Age is required and age must be a number
				return !($this->FullName == "" or $this->Age == "" or is_numeric($this->Age) == false) ;
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


			//new
			function Registration($json) {
				$this->JSON = $json;

				date_default_timezone_set('Australia/Melbourne');
				$this->DateTimeEntered = date("Y/m/d H:i:s");
			}

			function logError($err){
				$this->errMsg .= $err . "\n";
				if ($this->debug){
					echo $err;
				}
			}


			function parseJSON(){

					if ($this->JSON == ""){
						return false;
					}
					$rego = json_decode($this->JSON,true);
					
					//echo $rego['Name'];
					//echo $group['Registrants'][0]['Name'];


					//assign the property of the object from the json array
					$this->FullName        = $rego['Name'];
					$this->Age             = $rego['Age'];
					$this->Church          = $rego['Church'];
					$this->Email           = $rego['Email'];
					$this->Phone           = $rego['Phone'];
					$this->Airbed          = $rego['AirBedDiscount'];
					$this->AirportTransfer = $rego['AirportTransfer'];
					$this->Fee             = $rego['Fee'];
					$this->Comments        = $rego['Comments'];


					foreach ($rego['Registrants'] as $member) {

						
						//create the person object
						$newPerson = new Person();
						
						//assign property to person object from json array
						$newPerson->FullName        = $member['Name'];
						$newPerson->Age             = $member['Age'];
						$newPerson->Relation        = $member['Relation'];
						$newPerson->FamilyDiscount  = $member['DiscountFamily'];
						$newPerson->Airbed          = $member['AirBedDiscount'];
						$newPerson->AirportTransfer = $member['AirportTransfer'];
						$newPerson->Fee             = $member['Fee'];

						//add to array
						array_push($this->PersonStack, $newPerson);


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

			function generateRandomString($includeGUI = false)
			{
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


			    return $randomString;
			}			


			function commitDB(){
				$myJSON = $this->JSON;
				
				if ($myJSON == ""){
					$this->logError("No json data.");
					return false;
				}


				$mysqli = new mysqli("localhost", "melbou99_mysql", "daihoi2016!", "melbou99_mysql");
				if ($mysqli->connect_errno) {
				    $this->logError("Failed to connect to MySQL: (" . $mysqli->connect_errno . ") " . $mysqli->connect_error);
				}
				//echo $mysqli->host_info . "\n";

				$mysqli = new mysqli("127.0.0.1", "melbou99_mysql", "daihoi2016!", "melbou99_mysql", 3306);
				if ($mysqli->connect_errno) {
				    $this->logError("Failed to connect to MySQL: (" . $mysqli->connect_errno . ") " . $mysqli->connect_error);
				}
				//echo $mysqli->host_info . "\n" ;


				/* change character set to utf8 */
				if (!$mysqli->set_charset("utf8")) {
				    $this->logError("Error loading character set utf8:" . $mysqli->error);
				    exit();
				} else {
				    //printf("Current character set: %s\n", $mysqli->character_set_name());
				}


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
				if (!($stmt = $mysqli->prepare("INSERT INTO MainContact (FullName, Age, Church, Email, ContactNumber, DateTimeEntered, AirportTransfer, Airbed, Comments, Fee, Reference) VALUES ( ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?);"))) {
				    $this->logError("Prepare failed MainContact: (" . $mysqli->errno . ") " . $mysqli->error);
				}

				/* Prepared statement, stage 2: bind and execute */
				$FullName        = $this->FullName;
				$Age             = $this->Age;
				$Church          = $this->Church;
				$Email           = $this->Email;
				$ContactNumber   = $this->Phone;
				$DateTimeEntered = $this->DateTimeEntered;
				$AirportTransfer = $this->AirportTransfer;
				$Airbed          = $this->Airbed; 
				$Comments        = $this->Comments;
				$Fee             = $this->Fee; 


				if (!$stmt->bind_param("sssssssssss", $FullName, 
															$Age, 
															$Church, 
															$Email, 
															$ContactNumber, 
															$DateTimeEntered, 
															$AirportTransfer, 
															$Airbed, 
															$Comments, 
															$Fee,
															$Reference)) {
																
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
					if (!($stmt = $mysqli->prepare("INSERT INTO Registrant (MainContactId, FullName, Age, Relation, FamilyDiscount, Airbed, AirportTransfer,Fee) VALUES ( ?, ?, ?, ?, ?, ?, ?, ?)"))) {
					    $this->logError("Prepare failed members: (" . $mysqli->errno . ") " . $mysqli->error);
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
									
								
									if (!$stmt->bind_param("ssssssss", $newMainContactId, 
																		$FullName, 
																		$Age, 
																		$Relation,
																		$FamilyDiscount,
																		$Airbed, 
																		$AirportTransfer, 
																		$Fee)) {

									    $this->logError("Binding parameters failed members: (" . $stmt->errno . ")<br />  " . $stmt->error);
										$membersUpdateError =1;
									}

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


					//echo "done...!";


				}
				

				$mysqli->close();

				return true;

			}

			function ToYesNo($v){

				return ($v == "1" || $v == true || $v == 1) ? "Yes" : "No" ; 

			}

			function viewRego($ref){

					$mysqli = new mysqli("localhost", "melbou99_mysql", "daihoi2016!", "melbou99_mysql");
					if ($mysqli->connect_errno) {
					    echo "Failed to connect to MySQL: (" . $mysqli->connect_errno . ") " . $mysqli->connect_error;
					}
					//echo $mysqli->host_info . "\n";

					$mysqli = new mysqli("127.0.0.1", "melbou99_mysql", "daihoi2016!", "melbou99_mysql", 3306);
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



					// selects
					if ($res = $mysqli->query("SELECT C.*, R.FullName RName, R.Age RAge, R.Relation RRelation, R.FamilyDiscount RFamilyDiscount, R.Airbed RAirBed, R.AirportTransfer RAirportTransfer, R.Fee RFee  FROM MainContact C LEFT OUTER JOIN Registrant R ON R.MainContactId=C.MainContactId WHERE C.Reference = '" . $ref . "';")){


							if ($res->num_rows > 0) {
						



									//the row templates
				        			$row1 = "<tr><td>%s</td><td>%s</td><td>%s</td><td>%s</td><td>%s</td><td>%s</td><td>$%s</td></tr>";
				        			$row2 = "<tr><td>%s.</td><td>%s</td><td>%s</td><td>$%s</td></tr>";

									$memberRowHtml = "";
									$paymentRowHtml = "";

									$res->data_seek(0);
									$counter = 0;
									$membersFeeTotal = 0;

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


												//add to payment summary row
												$paymentRowHtml = sprintf($row2, $counter , $this->FullName, $this->Age, $this->Fee);

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

											//totals the members (not main contact)
											$membersFeeTotal = $membersFeeTotal + $newPerson->Fee;

											//add to registrant row
											if ($newPerson->isValid()){

												$memberRowHtml .= sprintf($row1, 
																			$newPerson->FullName, 
																			$newPerson->Age, 
																			$newPerson->Relation, 
																			$newPerson->FamilyDiscount, 
																			$this->ToYesNo($newPerson->Airbed),  
																			$this->ToYesNo($newPerson->AirportTransfer),  
																			$newPerson->Fee, null);


												//add to payment summary row
												$paymentRowHtml .= sprintf($row2, ($counter + 1) , $newPerson->FullName, $newPerson->Age, $newPerson->Fee);

												//add to array
												array_push($this->PersonStack, $newPerson);													

											}



									}		





									//finds template and formats to php formatted string
									$html = file_get_contents($_SERVER['DOCUMENT_ROOT'] . "/register/summary_template.html");
									for ($i=0; $i < 16 ;  $i++) { 
										$html = str_replace( "{" . $i . "}", "%s", $html);
									}
									//replace the js functions
									$html  = str_replace("submitRegistration(this)","window.print();",$html);
									$html  = str_replace("REGISTER ME !"," PRINT ",$html);
									$html  = str_replace("swapRegoSummary","return false;void",$html);

								
									echo sprintf($html, 
										'<span class="pull-right">Reference: <span class="label label-success label-summary-total">' . $this->Reference . '</span></span>', 
										$this->FullName, 
										$this->Age, 
										$this->Email, 
										$this->Phone, 
										$this->ToYesNo($this->Airbed), 
										$this->ToYesNo($this->AirportTransfer),
										$this->Church, 
										$this->Fee,
										$this->Comments,
										((count($this->PersonStack)) == 1) ? 'X1' : 'hidden', //hidden if no members
										$memberRowHtml,
										$membersFeeTotal,
										$paymentRowHtml,
										($membersFeeTotal + $this->Fee),
										null
										);
										

							}else{
								//no records
								echo  '<div class="alert alert-dismissible alert-danger"><button type="button" class="close" data-dismiss="alert">X</button><i class="fa fa-frown-o fa-5x pull-left"> </i><strong>Oh no!</strong> We could not find any records the given reference number. You can try to <a href="/contact/" class="alert-link">contact the conference team</a>. for help concerning your registration.<div class="clearfix"></div></div>';
								
							}



						/* free result set */
    					$res->close();
					}
		



				$mysqli->close();

				return true;


			}



		   
		} // end of class rego


		function viewRego(){
			if ($_GET["ref"] ){
				$rego = new Registration("");
				$rego->viewRego($_GET["ref"]);
			}else{

					echo '
							<form role="form"  class="horizontal-form" action="." method="get">
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
						';

			}

		}

		function processRegoSubmission(){

				$json = '{"Name":"Kyle","Age":"16","Church":"Hội Thánh Tin Lành Springvale (VECA)","Email":"kyle@instil.org.au","Phone":"+61404558997","AirBedDiscount":true,"AirportTransfer":true,"Fee":445,"Registrants":[{"Name":"JENNIFER HUYNH","Age":35,"Relation":"Wife","DiscountFamily":"-","AirBedDiscount":false,"AirportTransfer":true,"Fee":465},{"Name":"LARA HUYNH","Age":7,"Relation":"Daughter","DiscountFamily":"-","AirBedDiscount":false,"AirportTransfer":true,"Fee":375},{"Name":"EMMA HUYNH","Age":4,"Relation":"Daughter","DiscountFamily":"2nd child 5yo and under","AirBedDiscount":false,"AirportTransfer":true,"Fee":25},{"Name":"MARCUS","Age":1,"Relation":"Son","DiscountFamily":"2nd child over 5yo","AirBedDiscount":false,"AirportTransfer":true,"Fee":0},{"Name":"LAN TRAN","Age":50,"Relation":"Mum","DiscountFamily":"-","AirBedDiscount":true,"AirportTransfer":true,"Fee":445},{"Name":"","Age":null,"Relation":"-","DiscountFamily":false,"AirBedDiscount":false,"AirportTransfer":false,"Fee":0},{"Name":"","Age":null,"Relation":"-","DiscountFamily":false,"AirBedDiscount":false,"AirportTransfer":false,"Fee":0}],"Comments":"Phí bao gồm tất cả chi phí tại Hội nghị : chỗ ở, thực phẩm (ẩm thực Việt Nam - phục vụ của nhà thờ ) và tất cả các cơ sở trong thời gian hội nghị.\n\nGiao thông vận tải : Đưa đón sân bay từ và tới sân bay vào ngày 27 và 31 có thể được sắp xếp nếu cần - đặt phòng trước ngày yêu cầu và chi phí riêng biệt áp dụng.\n\nHội nghị sẽ bắt đầu vào tối ngày 27 tháng mười hai năm 2016 và kết thúc vào lúc trưa ngày 31 tháng 12 năm 2016 .\n\nThức ăn : món ăn Việt Nam sẽ là món ăn chính cung cấp tại 2.016 hội nghị Melbourne. cầu dinh dưỡng đặc biệt phải được thực hiện được biết đến tại thời điểm đăng ký - chúng tôi không thể đảm bảo tất cả các nhu cầu dinh dưỡng khác nhau có thể được đáp ứng.","Reference":"87ICF324I6"}';



				if( $_POST["json"] || $_POST["reference"] ) {

			      	if ($_POST["json"] <> ""){

						$rego = new Registration($_POST["json"]);
						$rego->parseJSON();
						
						//$rego->toString();
						if ($rego->commitDB()){
							echo '{"status": 1, "reference": "' . $rego->Reference . '","message":""}';
						}else{
							echo '{"status": 0, "reference":"" ,"message": "' . $rego->errMsg . '"}';
						}
					
					}

			   }
		}

			


	?>
