<?php
class TradedoublerDiscount extends WC_Discounts {
    public function is_coupon_valid_limited( $coupon ) {
        try {
            $this->validate_coupon_exists( $coupon );
            $this->validate_coupon_usage_limit( $coupon );
            $this->validate_coupon_user_usage_limit( $coupon );
            $this->validate_coupon_expiry_date( $coupon );

            if ( ! apply_filters( 'woocommerce_coupon_is_valid', true, $coupon, $this ) ) {
                throw new Exception( __( 'Coupon is not valid.', 'woocommerce' ), 100 );
            }
        } catch ( Exception $e ) {
            /**
             * Filter the coupon error message.
             *
             * @param string    $error_message Error message.
             * @param int       $error_code    Error code.
             * @param WC_Coupon $coupon        Coupon data.
             */
            $message = apply_filters( 'woocommerce_coupon_error', is_numeric( $e->getMessage() ) ? $coupon->get_coupon_error( $e->getMessage() ) : $e->getMessage(), $e->getCode(), $coupon );

            return new WP_Error(
                'invalid_coupon',
                $message,
                array(
                    'status' => 400,
                )
            );
        }
        return true;
    }
}