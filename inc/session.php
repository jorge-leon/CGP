<?php
// initialize session
session_start();
 
if(!isset($_SESSION['user'])) {
	// user is not logged in, redirect to authentication
	header("Location: auth.php");
	die();
}
