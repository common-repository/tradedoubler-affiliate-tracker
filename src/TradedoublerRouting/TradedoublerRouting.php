<?php

class TradedoublerRouting
{

    /**
     * @throws Exception
     */
    static public function postRouting()
    {
        $tab = ! isset($_GET['tab']) ? 'main' : $_GET['tab'];


        foreach ($_POST as $key => $value) {
            $_SESSION['form_tb'][$key] = $value;
        }

        switch ($tab) {
//            case 'step2':
//                TradedoublerViews::verifyPasswordLogin();
//                break;
//            case 'step3':
//                TradedoublerViews::verifyNonPasswordLoginData();
//                break;
//            case 'step4':
//                TradedoublerViews::processForm();
//                break;
            case 'step5':
                TradedoublerViews::createProgramForm();
                break;
            case 'settings':
                TradedoublerViews::updateProgramForm();
                break;
            case 'tracking':
                TradedoublerViews::trackingForm();
                break;
            case 'signInForm':
                unset($_SESSION['form_tb']);
                TradedoublerViews::signInForm();
                break;
        }
    }

    static public function routing()
    {
        self::getRouting();
    }

    static public function getRouting()
    {
        $page = ! isset($_GET['page']) ? 'tradedoubler' : $_GET['page'];
        $tab  = ! isset($_GET['tab']) ? 'main' : $_GET['tab'];

        if ( !class_exists('WooCommerce', true)){
            return;
        }

        if (isset($_GET['logout']) && $_GET['logout'] == '1') {
            TradedoublerViews::logout();
        }

        if (self::userIsActive()) {

            if ($tab != 'step4' && ! self::userHasProgram()) {
                $step4url
                    = admin_url('admin.php?page=tradedoubler&tab=step4');
                TradedoublerViews::redirect($step4url);
            }

            switch ($tab) {
                case 'products':
                    TradedoublerViews::products();
                    break;
                case 'codes':
                    TradedoublerViews::codes();
                    break;
                case 'tracking':
                    TradedoublerViews::tracking();
                    break;
                case 'settings':
                    TradedoublerViews::settings();
                    break;
                case 'main':
                default:
                    TradedoublerViews::dashboard();
                    break;
                case 'step4':
                    TradedoublerViews::step4();
                    break;
//                case 'step5':
//                    TradedoublerViews::step5();
//                    break;
            }
        } else {
            switch ($tab) {

                case 'main':
                default:
                    TradedoublerViews::signIn();
                    break;
//                case 'step1':
//                    TradedoublerViews::step1();
//                    break;
//                case 'step2':
//                    TradedoublerViews::step2();
//                    break;
//                case 'step3':
//                    TradedoublerViews::step3();
//                    break;
//                case 'step4':
//                    TradedoublerViews::step4();
//                    break;
            }


        }

    }


    static public function userIsActive()
    {
        return is_array(get_option('tradedoubler_credentials', ''));
    }

    static public function userHasProgram()
    {
        return ((int)get_option('tradedoubler_program_ID', '')) !== 0;
    }
}