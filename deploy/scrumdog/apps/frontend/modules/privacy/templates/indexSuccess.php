<?php slot('page_title') ?>Privacy Policy<?php end_slot() ?>
<?php slot('page_heading') ?>Privacy Policy<?php end_slot() ?>
<div id="main">
<h1>Privacy Policy</h1>
<h3>Table Of Contents</h3>
<a href="#commitment-to-privacy">Our Commitment To Privacy</a><br />
<a href="#information-we-collect">The Information We Collect</a><br />
<a href="#how-we-use-information">How We Use Information</a><br />
<a href="#data-security">Our Commitment To Data Security</a><br />
<a href="#access-and-correct-information">How To Access Or Correct Your Information</a><br />
<a href="#contact-us">How To Contact Us</a><br />

<br />
<a name="commitment-to-privacy"></a><h2>Our Commitment To Privacy</h2>
<p>Your privacy is important to us. To better protect your privacy we provide this notice explaining our online information practices and the choices you can make about the way your information is collected and used. To make this notice easy to find, we make it available on the footer of every page of the site and highlight it at every point where personally identifiable information may be requested.</p>

<a name="information-we-collect"></a><h2>The Information We Collect</h2>
<p>This notice applies to all information collected or submitted on the ScrumDog website. When you register on the site, we collect the following information:</p>
<ul>
	<li>Name</li>
	<li>Password</li>
	<li>Gender</li>
	<li>Email address</li>
	<li>Phone number</li>
	<li>City</li>
	<li>State</li>
	<li>Country</li>
</ul>
<p>On some pages, you can submit information about other people. For example, if you invite someone to join ScrumDog, you will need to submit the person's email address.</p>

<a name="how-we-use-information"></a><h2>The Way We Use Information</h2>
<p>We use the information you provide about yourself to provide a better experience for other users.
We never use or share or sell the personally identifiable information provided to us online in ways unrelated to the ones described above without also providing you an opportunity to opt-out or otherwise prohibit such unrelated uses.</p>

<a name="data-security"></a><h2>Our Commitment To Data Security</h2>
<p>To prevent unauthorized access, maintain data accuracy, and ensure the correct use of information, we have put in place appropriate physical, electronic, and managerial procedures to safeguard and secure the information we collect online.</p>

<a name="access-and-correct-information"></a><h2>How You Can Access Or Correct Your Information</h2>
<p>You can access all your personally identifiable information that we collect online and maintain by editing your profile.
You can correct factual errors in your personally identifiable information by sending us a request that credibly shows error.
To protect your privacy and security, we will also take reasonable steps to verify your identity before granting access or making corrections.</p>

<a name="contact-us"></a><h2>How To Contact Us</h2>
<p>Should you have other questions or concerns about these privacy policies, please send us an email at
<?php echo Fluide_Symfony_Util::emailLink(sfConfig::get('app_info_email')) ?>.</p>
<br />
</div>
<div id="sidebar">
<?php if($isAuthenticated): ?>
  <?php include_component('user', 'projects') ?>
  <?php include_component('default', 'inviteMembers') ?>
<?php else: ?>
	<?php include_component('auth', 'register'); ?>
<?php endif; ?>
</div>