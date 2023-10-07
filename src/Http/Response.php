<?php

namespace MMAE\WPPlugin\Http;

use WpOrg\Requests\Utility\CaseInsensitiveDictionary;

class Response {

	/**
	 * @param array|\WP_Error $response
	 *
	 * @return Response
	 */
	public static function make( array|\WP_Error $response ): Response {
		return new self( $response );
	}

	/**
	 * @param array|\WP_Error $response
	 */
	public function __construct( private array|\WP_Error $response ) {}

	/**
	 * @return string
	 */
	public function body(): string {
		return wp_remote_retrieve_body( $this->response );
	}

	/**
	 * @return bool
	 */
	public function error(): bool {
		return is_wp_error( $this->response );
	}

	/**
	 * @return bool
	 */
	public function success(): bool {
		return ! $this->error();
	}

	/**
	 * @return int|string
	 */
	public function code(): int|string {
		if ( $this->success() ) {
			return wp_remote_retrieve_response_code( $this->response );
		}

		return $this->response->get_error_code();
	}

	/**
	 * @return string
	 */
	public function message(): string {
		if ( $this->success() ) {
			return wp_remote_retrieve_response_message( $this->response );
		}

		return $this->response->get_error_message( $this->response->get_error_code() );
	}

	public function messages(): array {
		if ( $this->success() ) {
			return [];
		}

		return $this->response->get_error_messages();
	}

	public function header(): CaseInsensitiveDictionary|array {
		return wp_remote_retrieve_headers( $this->response );
	}

}