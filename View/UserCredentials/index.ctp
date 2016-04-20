<form method="POST" action="<?=$this->request->query['callbackUrl']?>">
	<p>Username: <input type="text" name="username" /></p>
	<p>Password: <input type="password" name="password" /></p>
	<p><button>Submit</button></p>
</form>