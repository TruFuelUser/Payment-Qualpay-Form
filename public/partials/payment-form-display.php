<?php

/**
 * Provide a public-facing view for the plugin
 *
 * This file is used to markup the public-facing aspects of the plugin.
 *
 * @link       https://trufuel.net
 * @since      1.0.0
 *
 * @package    Contact_Form_Trucore
 * @subpackage Contact_Form_Trucore/public/partials
 */
?>

<!-- This file should primarily consist of HTML with a little bit of PHP. -->
 <!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Payment</title>
    <!-- Librerías de estilo -->
    <link rel="stylesheet" type="text/css" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.7.2/css/all.min.css">
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.8.1/css/all.css">

    <!-- Librerias de javascript -->
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.7.1/jquery.min.js"></script>

    
    <!-- GOOGLE FONTS LIBRARY -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Afacad:ital,wght@0,400..700;1,400..700&family=Inter:ital,opsz,wght@0,14..32,100..900;1,14..32,100..900&family=Lexend:wght@100..900&family=Montserrat:ital,wght@0,100..900;1,100..900&family=Nunito:ital,wght@0,200..1000;1,200..1000&family=Outfit:wght@100..900&family=Poppins:ital,wght@0,100;0,200;0,300;0,400;0,500;0,600;0,700;0,800;0,900;1,100;1,200;1,300;1,400;1,500;1,600;1,700;1,800;1,900&family=Wix+Madefor+Text:ital,wght@0,400..800;1,400..800&display=swap" rel="stylesheet">

    <!-- API reCAPTCHA -->
    <script src="https://www.google.com/recaptcha/api.js"></script>
<?php
    $trf_recaptcha_site_key = get_option( 'trf_recaptcha_site_key', '' );
    if ( $trf_recaptcha_site_key !== '' ) :
?>
    <script src="https://www.google.com/recaptcha/enterprise.js?render=<?php echo esc_attr( $trf_recaptcha_site_key ); ?>"></script>
    <?php endif; ?>
</head>
<body>
<div class="container py-5">
    <div class="row mb-4">
        <div class="col-lg-8 mx-auto text-center">
            <h1 class="display-6">Payment</h1>
        </div>
    </div>
    <div class="row">
        <div class="col-lg-6 mx-auto">
            <div class="card">
                <div class="card-header">
                    <div class="bg-white shadow-sm pt-4 pl-2 pr-2 pb-2">
                        <ul role="tablist" class="nav bg-light nav-pills rounded nav-fill mb-3">
                            <li class="nav-item"><a data-toggle="pill" href="#credit-card" class="nav-link active"><i class="fas fa-credit-card mr-2"></i>  Credit Card </a></li>
                        </ul>
                    </div>
                    <div class="tab-content">
                        <div id="credit-card" class="tab-pane fade show active pt-3">
                            <form role="form" onsubmit="event.preventDefault()" id="trf-payment-form">
                                <div class="form-group">
                                 <!-- CARD OWNER -->
                                    <label for="username"><h6 class="required">Card Owner</h6></label>
                                    <input type="text" name="form_fields[cardOwner]" placeholder="Card Owner Name" class="form-control" required id="CardOwner">
                                    <div class="invalid-feedback">
                                      
                                    </div>
                                </div>
                                <!-- CARD NUMBER -->
                                <div class="form-group">
                                    <label for="cardNumber"><h6 class="required">Card Number</h6></label>
                                    <div class="input-group">
                                        <input type="text" name="form_fields[cardNumber]" placeholder="Valid Card Number" class="form-control" required id="CardNumber">
                                        <div class="input-group-append">
                                            <span class="input-group-text text-muted">
                                                <i class="fab fa-cc-visa mx-1"></i>
                                                <i class="fab fa-cc-mastercard mx-1"></i>
                                                <i class="fab fa-cc-amex mx-1"></i>
                                                <i class="fab fa-cc-discover mx-1"></i>
                                            </span>
                                        </div>
                                        <!-- <span class='ValidateInput'></span> -->
                                        <div class="invalid-feedback">
                                               
                                        </div>
                                        <div class="invalid-feedback" id="CardNumberValidate">

                                        </div>
                                    </div>
                                </div>
                                <div class="row">
                                 <!-- EXPIRATION DATE -->
                                    <div class="col-sm-8">
                                        <div class="form-group">
                                            <label><h6 class="required">Expiration Date</h6></label>
                                            <div class="input-group">
                                                <input type="number" placeholder="MM" name="form_fields[month]" class="form-control" required id="monthCard">
                                                <input type="number" placeholder="YY" name="form_fields[year]" class="form-control" required id="yearCard">
                                            </div>
                                            <!-- <span class='ValidateInput'></span> -->
                                            <div class="invalid-feedback">
                                               
                                            </div>
                                            <div class="invalid-feedback" id="ExpirationValidate">

                                            </div>
                                        </div>
                                    </div>
                                <!-- CVV -->
                                    <div class="col-sm-4">
                                        <div class="form-group mb-4"><label data-toggle="tooltip" title="Three digit CV code on the back of your card">
                                            <label><h6 class="required">CVV <i class="fa fa-question-circle d-inline"></i></h6></label>
                                            <input type="number" name="form_fields[cvv]" required class="form-control" placeholder="CVV" id="CVV">
                                            <div class="invalid-feedback">
                    
                                            </div>
                                            <div class='invalid-feedback' id='ValidateCVV'></div>
                                        </div>
                                    </div>
                                </div>
                             <!-- ADDRESS -->
                                <div class="row">
                                    <div class="col-sm-8">
                                        <div class="form-group">
                                            <label><h6>Address</h6></label>
                                                <input type="text" placeholder="Street address of the cardholder" name="form_fields[address]" class="form-control" id="address">
                                            <!-- <span class='ValidateInput'></span> -->
                                            <div class="invalid-feedback">
                                               
                                            </div>
                                            <div class="invalid-feedback" id='ValidateAddress'>
                                               
                                            </div>
                                        </div>
                                    </div>
                                 <!-- ZIP CODE -->
                                    <div class="col-sm-4">
                                        <div class="form-group mb-4">
                                            <label><h6 class="required">Zip Code</h6></label>
                                            <input type="number" name="form_fields[zipCode]" required class="form-control" id="ZipCode" placeholder="Zip Code">
                                            <div class="invalid-feedback">
                    
                                            </div>
                                            <div class="invalid-feedback" id="ValidateZipCode">
                    
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                  <div class="row">
                                <!-- COMPANY -->
                                     <div class="col-sm-8">
                                        <div class="form-group mb-4">
                                            <label for="company"><h6>Company</h6></label>
                                            <input type="text" name="form_fields[company]" class="form-control" id="company" placeholder="Company">
                                            <div class="invalid-feedback">
                    
                                            </div>
                                        </div>
                                    </div>
                                <!-- ACCOUNT NUMBER -->
                                    <div class="col-sm-4">
                                        <div class="form-group">
                                            <label for="account-number"><h6>Account Number</h6></label>
                                                <input type="number" placeholder="Account #" name="form_fields[acctNo]" class="form-control" id="account-number">
                                            <!-- <span class='ValidateInput'></span> -->
                                            <div class="invalid-feedback">
                                               
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <!-- INVOICE # -->
                                 <div class="form-group">
                                        <label for="invoice"><h6>Invoice #</h6></label>
                                    
                                        <input type="number" name="form_fields[invoice]" placeholder="Invoice #" class="form-control"  id="invoice">
                                        <div class="invalid-feedback">
                                            
                                        </div>
                                </div>
                                <div class="form-group">
                                        <label for="email"><h6>Email</h6></label>
                                    
                                        <input type="email" name="form_fields[email]" placeholder="Email@example.com" class="form-control"  id="email">
                                        <small id="emailHelp" class="form-text text-muted">Please include email to receive payment receipt.</small>
                                        <div class="invalid-feedback">
                                            
                                        </div>
                                </div>
                                <?php  if(isset($_GET['amt'])) { ?>
                                    <div class="form-group">
                                        <label for="amount"><h6 class="required">Amount $</h6></label>
                                    
                                        <input type="float" name="form_fields[amount]" placeholder="Amount" required class="form-control" value="<?php echo $_GET['amt'] ?>" id="amount">
                                        <div class="invalid-feedback">
                                            
                                        </div>
                                        <div class="invalid-feedback" id='ValidateAmount'>

                                        </div>
                                    </div>
                                    <?php   }else{ ?>
                                        <div class="form-group">
                                            <label for="amount"><h6 class="required">Amount $</h6></label>
                                        
                                            <input type="float" name="form_fields[amt]" placeholder="Amount" required class="form-control" id="amount">
                                            <div class="invalid-feedback">
                                                
                                            </div>
                                            <div class="invalid-feedback" id='ValidateAmount'>
                                            
                                            </div>
                                         </div>
                                    <?php }   ?>
                                <input type="hidden" name="form_fields[token]" id="token">
                                <input type="hidden" name="form_fields[transactionID]" id="transaction-id">
                                <div class="card-footer">
                                    <button type="submit" class="btn btn-primary btn-block" id="submit-btn-payment">Confirm Payment </button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div id="qp-embedded-container" class="col-lg-10 mx-auto" align="center"></div>
    <div id="email-message" class="col-lg-10 mx-auto" align="center"></div>
</body>
</html>

