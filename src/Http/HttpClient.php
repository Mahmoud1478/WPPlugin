<?php

namespace MMAE\WPPlugin\Http;

class HttpClient {
	private array $headers = [];

	/**
	 * @param string $url
	 * @param array $query
	 *
	 * @return Response
	 */
	function get( string $url, array $query = [] ): Response {
		return Response::make( wp_remote_request( esc_url_raw( $url ) . http_build_query( $query ), [
			'method' => 'GET',
			'header' => $this->headers
		] ) );
	}

	/**
	 * @param string $url
	 * @param array $body
	 *
	 * @return Response
	 */
	function post( string $url, array $body = [] ): Response {
		return Response::make( wp_remote_request( esc_url_raw( $url ), [
			'method' => 'POST',
			'header' => $this->headers,
			'body'   => $body,
		] ) );
	}

	/**
	 * @param string $url
	 * @param array $body
	 *
	 * @return Response
	 */
	function put( string $url, array $body ): Response {
		return Response::make( wp_remote_request( esc_url_raw( $url ), [
			'method' => 'PUT',
			'header' => $this->headers,
			'body'   => $body,
		] ) );
	}

	/**
	 * @param string $url
	 * @param array $body
	 *
	 * @return Response
	 */
	function patch( string $url, array $body ): Response {
		return Response::make( wp_remote_request( esc_url_raw( $url ), [
			'method' => 'PATCH',
			'header' => $this->headers,
			'body'   => $body,
		] ) );
	}

	/**
	 * @param string $url
	 * @param array $body
	 *
	 * @return Response
	 */
	function delete( string $url, array $body ): Response {
		return Response::make( wp_remote_request( esc_url_raw( $url ), [
			'method' => 'DELETE',
			'header' => $this->headers,
			'body'   => $body,
		] ) );
	}

	/**
	 * @param string $username
	 * @param string $password
	 *
	 * @return $this
	 */
	public function setBaseAuth( string $username, string $password ): static {
		$this->headers['Authorization'] = 'Basic ' . base64_encode( $username . ':' . $password );

		return $this;
	}

	/**
	 * @param string $token
	 *
	 * @return $this
	 */
	public function setBearerToken( string $token ): static {
		$this->headers['Authorization'] = 'Bearer ' . $token;

		return $this;
	}

	/**
	 * @param array $headers
	 *
	 * @return $this
	 */
	public function setHeaders( array $headers ): static {
		$this->headers = $headers;

		return $this;
	}
}