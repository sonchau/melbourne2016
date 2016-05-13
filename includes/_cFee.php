<?php 


	class FeeCalculator{


    	const FAMILY_DISCOUNT1_AMOUNT = 50;

	    const FAMILY_DISCOUNT2_AMOUNT = 100;

	    const AIRBED_DISCOUNT_AMOUNT = 20;

	    const AIRPORT_FEE = 25;

	    const EARLY_BIRD_DISCOUNT_AMOUNT1 = 30;

	    const EARLY_BIRD_DISCOUNT_AMOUNT2 = 40;


		function FeeCalculator(){


		}


		function calculateFee(	$Age = 0, 
								$FamilyDiscount = '-', 
								$Airbed = 0, 
								$AirportTransfer = 0){



		        $fee = 0;

		        //normal pricing
	            switch (true) {
	                case ($Age <= 5):
	                    $fee = 50;
	                    break;

	                case ($Age > 5 && $Age <= 11):
	                    $fee = 350;
	                    break;

	                case ($Age > 11 && $Age < 65):
	                    $fee = 440;
	                    break;
	                case ($Age >= 65):
	                    $fee = 390;
	                    break;
                    default:

						# code...

						break;

	            }



	            //any family discounts
				switch ($FamilyDiscount) {

					case '2nd child 5yo or under':
						//only apply if age is under 6
						if ($Age < 6){
							$fee = $fee - self::FAMILY_DISCOUNT1_AMOUNT;
						}
						break;

					case '2nd child over 5yo':
						$fee = $fee - self::FAMILY_DISCOUNT2_AMOUNT;
						break;

					default:
						# code...
						break;					

				} 



				if ($Airbed) { //airbed discount
					$fee = $fee - self::AIRBED_DISCOUNT_AMOUNT;
				}


				//airport transfer fee
				if ($AirportTransfer) {
					$fee = $fee + self::AIRPORT_FEE;

				}


				//early bird special
				$fee = $this->calculateEarlyBirdDiscount($fee, $Age);



				//adjustment of fee
        		if ($fee < 0) { 
        			$fee = 0 ;
        		}


            	return $fee;

			}




			function calculateEarlyBirdDiscount($fee, $age){
				if (is_numeric($fee)){

					if (new DateTime() < new DateTime("2016-09-30 00:00:00")) {


				            switch (true) {
				                case ($age <= 5):
				                    //no eary bird discount
				                    break;

				                case ($age > 5 && $age <= 11):
				                    $fee = $fee - self::EARLY_BIRD_DISCOUNT_AMOUNT1;
				                    break;

				                case ($age > 11 && $age < 65):
				                    $fee = $fee - self::EARLY_BIRD_DISCOUNT_AMOUNT2;
				                    break;

				                case ($age >= 65):
				                    $fee = $fee - self::EARLY_BIRD_DISCOUNT_AMOUNT2;
				                    break;

			                    default:
									# code...
									break;

				            }

				    	
				    		return $fee;
					}

				}else{
					return $fee;
				}
		
			}




	//end class
	}


?>