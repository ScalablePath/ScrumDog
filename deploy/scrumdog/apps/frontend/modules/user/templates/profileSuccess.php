<?php slot('page_title') ?>Member Profile - <? echo($user->getFullName()); ?><?php end_slot() ?>
<div id="main">
<h1>Member Profile: <? echo($user->getUsername()); ?></h1>
<div class="box">
	<?php echo image_tag($profileImageSrc, array('alt'=>$user->getFullName(),'title'=>$user->getFullName())) ?>
  <h2><? echo($user->getFullName()); ?>
  <? if($is_current_user): ?>
    (<a href="<? echo(url_for('@member_editprofile')) ?>">edit profile</a>)
  <? endif; ?>
  </h2>
  <table class="profile">
    <tbody>
    <tr><td>Email:</td><td><? echo($user->getEmail()); ?></td></tr>
    <tr><td>Phone Number:</td><td><? echo($user->getPhone()); ?></td></tr>
    <tr><td>Gender:</td><td><? echo($user->getGender()); ?></td></tr>
    <tr><td>Location:</td>
      <td>
<? 
if(trim($user->getCity())!='')
{
  echo($user->getCity());
}
if(trim($user->getState())!='')
{
  echo(', '.$user->getState());
}
if(trim($user->getCountry())!='')
{
  echo(', '.$user->getCountry());
}
?>
      </td>
    </tr>
  </table>
</div>
</div>
<div id="sidebar">
    <?php include_component('user', 'projects') ?>
</div>