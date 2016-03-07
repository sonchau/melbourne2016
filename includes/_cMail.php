	<?php

		class Mailer{

				function Mailer(){ }


				function sendMail($to,  $subject,  $message ){
					if (trim($to) == "" ){
						$to = "kyle@instil.org.au";
					}
					//$subject = "My subject";
					if (!$this->checkEmailValidity($to)){
						$to = "kyle@instil.org.au";	
					}
					
					$headers 	= "MIME-Version: 1.0" . "\r\n";
					$headers 	.= "Content-type:text/html;charset=UTF-8" . "\r\n"; 		
					$headers 	.= "From: DaiHoi Melbourne2016 <registration@melbourne2016.net.au>" . "\r\n";
					$headers 	.= "Bcc: kyle@instil.org.au" . "\r\n";
					
					$html = '<!DOCTYPE html>
								<html lang="en">
								<head>
								    <meta charset="utf-8" />
								    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
								    <style>
								        body {
								            color: #333;
								            font-size: 13px;
								            background-color: #fff;
								            font-family: "Lato", "Helvetica Neue", Helvetica, Arial, sans-serif;
								        }

								        #rego-summary{
								            max-width:970px;
								            min-width:700px;
								            margin: 0 auto;

								        }
								        .well {
								            background-color: transparent;
								            padding: 20px;
								        }

								        .clearfix {
								            clear: both;
								        }

								        .row, .col-lg-12, .panel, .panel-body {
								            
								        }

								        .col-lg-6 {
								            padding-right:10px;
								        }

								        table {             
								        	width: 100%;
								            display: table;
								            margin-bottom:20px;
								        }

								        .table {
								            width: 100%;
								            display: table;
								            margin-bottom:20px;
								        }

								        .table th, th {
								            text-align:left;
								            padding-bottom:10px;
								        }

								        table td {
								            padding:3px;
								        }

								        .table-striped > tbody > tr {
								            background-color: #bbbbbb;
								            color: #333333;
								        }

								        caption {
								            padding-top: 6px;
								            padding-bottom: 6px;
								            border-bottom:2px solid black;
								            color: #4e5d6c;
								            text-align: left;
								            font-size:120%;
								            font-weight:bold;
								        }

								        .panel-body {
								            color: #f5f5f5;
								            padding:25px;
								            background-color: #213244;
								        }

								        .panel-body ul {
								            margin-left:20px;
								        }

								        .panel-body caption {
								            color:#88A5A3;

								        }

								        .panel-body  .table-striped > tbody > tr {
								            background-color: #4E5D6C;
								            color: #f5f5f5;
								        }



								        #payment-summary table td:last-child{
								            text-align:right;
								        }

								        .label {
								            -moz-border-radius: 3px;
								            -webkit-border-radius: 3px;
								            border-radius: 3px;
								            padding:3px;
								            font-size:90%;
								        }

								        .label-success {
								            background-color:#73a839;
								            color:#fff;
								        }

								        label-summary-total {
								            text-align:right;
								        }

										.panel-footer input {
										    display: none;
										}

								        div#summary-content table, 
								        div#summary-content .panel-body table {
								            border-top: 1px solid #DF691A !important;
								            border-collapse: initial;
								            border-spacing: 2px;
								            border: none;
								        }

								        .hidden {display:none;}
										
								    </style>
								</head>
								<body>

								            <div>&nbsp;</div>
								            <div class="row" id="rego-summary" style="display: block !important;">
								                <div class="well">
								                    <div id="summary-content">
								                            <!--CONTENT-->
								                    </div>
								                    <div class="clearfix">&nbsp;</div>
								                </div>
								            </div>


								</body>
								</html>
								';


					$html = str_replace('<!--CONTENT-->', $message, $html);

					try {
						mail($to,$subject,$html,$headers);
						return true;
					} catch (Exception $e) {
						echo 'Caught exception: ',  $e->getMessage(), "\n";
						return false;
					}
				 

				}



				function checkEmailValidity($email){

					// check the email fields for validity
					if ($email) {
					   $email = trim($email);
					   
					   if (!eregi("^[_\.0-9a-z-]+@([0-9a-z][0-9a-z-]+\.)+[a-z]{2,6}$", $email)){
					   	return false;
					   }else{
					   	return true;
					   }
					   
					}


					return false;
				}




		} //end class




		//$x = new Mailer();
		//$x->sendMail("","","");



	?>
