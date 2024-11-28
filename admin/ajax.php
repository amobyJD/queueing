<?php
ob_start();
$action = $_GET['action'];
include 'admin_class.php';
$crud = new Action();

if($action == 'login'){
	$login = $crud->login();
	if($login)
		echo $login;
}
if($action == 'login2'){
	$login = $crud->login2();
	if($login)
		echo $login;
}
if($action == 'logout'){
	$logout = $crud->logout();
	if($logout)
		echo $logout;
}
if($action == 'logout2'){
	$logout = $crud->logout2();
	if($logout)
		echo $logout;
}
if($action == 'save_user'){
	$save = $crud->save_user();
	if($save)
		echo $save;
}
if($action == 'delete_user'){
	$save = $crud->delete_user();
	if($save)
		echo $save;
}
if($action == 'signup'){
	$save = $crud->signup();
	if($save)
		echo $save;
}
if($action == "save_settings"){
	$save = $crud->save_settings();
	if($save)
		echo $save;
}
if($action == "save_transaction"){
	$save = $crud->save_transaction();
	if($save)
		echo $save;
}
if($action == "delete_transaction"){
	$save = $crud->delete_transaction();
	if($save)
		echo $save;
}
if($action == "save_window"){
	$save = $crud->save_window();
	if($save)
		echo $save;
}
if($action == "delete_window"){
	$save = $crud->delete_window();
	if($save)
		echo $save;
}
if($action == "save_uploads"){
	$save = $crud->save_uploads();
	if($save)
		echo $save;
}
if($action == "delete_uploads"){
	$save = $crud->delete_uploads();
	if($save)
		echo $save;
}

if($action == "save_queue"){
	$save = $crud->save_queue();
	if($save)
		echo $save;
}
if($action == "get_queueid"){
    $id = $_GET['id'];
    $queue = $crud->get_queueid($id);
    echo json_encode($queue);
}

if($action == "get_queue"){
	$get = $crud->get_queue();
	if($get)
		echo $get;
}
if($action == "get_queue_user"){
	$get = $crud->get_queue_user();
	if($get)
		echo $get;
}
if($action == "get_serve_user"){
	$get = $crud->get_serve_user();
	if($get)
		echo $get;
}
if($action == "get_wait"){
	$get = $crud->get_wait();
	if($get)
		echo $get;
}
if($action == "update_queue"){
	$update = $crud->update_queue();
	if($update)
		echo $update;
}
if($action == "done_queue"){
	$update = $crud->done_queue();
	if($update)
		echo $update;
}
