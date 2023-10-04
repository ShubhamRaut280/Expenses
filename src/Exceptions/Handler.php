<?php
namespace Simcify\Exceptions;

use Exception;
use Pecee\Handlers\IExceptionHandler;
use Pecee\Http\Request;
use Pecee\SimpleRouter\Exceptions\NotFoundHttpException;
use Pecee\Http\Middleware\Exceptions\TokenMismatchException;

class Handler implements IExceptionHandler {

    /**
     * Handle an error that occurs when routing has began
     * 
     * @param   \Pecee\Http\Request $request
     * @param   \Exception          $error
     * @return  mixed
     */
	public function handleError(Request $request, Exception $error) {
        /* The router will throw the NotFoundHttpException on 404 */
        if($error instanceof NotFoundHttpException) {

            return $request->setRewriteUrl(url('/404'));

        }

		/* Invalid crsr token */
		if($error instanceof TokenMismatchException) {
            header('Content-type: application/json');
            exit(json_encode(responder("error", "Hmm!", "Token mismatch, please reload page to update token.","reload();")));
		}

		throw $error;

	}

}
