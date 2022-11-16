<?php

ini_set('display_errors', 'On');
error_reporting(E_ALL);

include_once 'vendor/autoload.php';

use Simcify\Application;
use Simcify\Database;
use Simcify\Auth;
use Simcify\Mail;

$app = new Application();

$today = date("Y-m-d");
$companies = Database::table("companies")->where("id",">", 1)->where("reminders", 1)->get();

/**
 * Send signing reminders
 * 
 */
if ( count($companies) > 0 ) {
	foreach ($companies as $company) {
		$reminders = Database::table("reminders")->where("company", $company->id)->get();
		if ( count($reminders) > 0 ) {
			foreach ($reminders as $reminder) {
				$requestDate = date('Y-m-d', strtotime($today. ' - '.$reminder->days.' days'));
				$requests = Database::table("requests")->where("time_",">=", $requestDate)->where("time_","<=", $requestDate." 23:59:59.999")->get();
        		$sender = Database::table("users")->where("id", $request->sender)->first();
				if ( count($requests) > 0 ) {
					foreach ($requests as $request) {
						Mail::send(
			                $request->email, $reminder->subject,
			                array(
			                    "title" => "Document Signing reminder",
			                    "subtitle" => "Click the link below to respond to the invite.",
			                    "buttonText" => "Sign Now",
			                    "buttonLink" => $signingLink,
			                    "message" => "You have been invited to sign a document by ".$sender->fname." ".$sender->lname.". Click the link above to respond to the invite.<br><strong>Message:</strong> ".$reminder->subject."<br><br>Cheers!<br>".env("APP_NAME")." Team"
			                ),
			                "withbutton"
			            );
					}
				}else{
					continue;
				}
			}
		}else{
			continue;
		}
	}
}
