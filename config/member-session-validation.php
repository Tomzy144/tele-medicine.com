<?php
	if(empty($s_member_id)){
		session_destroy();
?>
		<script>
        window.parent(location="<?php echo $website_url?>/login");
        </script>
<?php 
		exit;
	}else{
        $fetch_user=$callclass->_get_member_details($conn, $s_member_id);
        $user_array = json_decode($fetch_user, true);
        $fullname= $user_array[0]['fullname'];
        $passport= $user_array[0]['passport'];
		$last_login= $user_array[0]['last_login'];
}?>

<?php
// 	if(empty($s_admin_id)){
// 		session_destroy();
// ?
// 		script>
//         window.parent(location="<?php echo $website_url?/login/");
//         /script>
//php }?>