<? BForm::$type = BForm::HORIZONTAL ?>

<h2>Login</h2>
<? if ($message) : ?>
	<h3 class="message">
		<?= $message; ?>
	</h3>
<? endif; ?>

<?= BForm::open('user/login'); ?>

<?= BForm::input('username', HTML::chars(Arr::get($_POST, 'username')), array('required' => '')); ?>

<?= BForm::password('password', NULL, array('required' => '')); ?>

<?= BForm::checkbox('remember', NULL, NULL, NULL, 'Remember me', __('Remember Me keeps you logged in for 2 weeks')); ?>

<? if ($show_actions): ?>
      <?= BForm::form_actions(BForm::submit('login', 'Login')); ?>
<? endif; ?>

<?= BForm::close(); ?>

<p class="pull-right">Or <?= HTML::anchor('user/create', 'create a new account'); ?></p>