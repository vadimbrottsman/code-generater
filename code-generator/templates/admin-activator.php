<?php
    /*
    * Code Generator шаблон страницы активации кодов
    */
?>

<div class="code-generator-wrap-activator">
	<div class="code-generator__block-activator">
		<h1>Активатор кодов</h1>
		<p>Введите код</p>
		<form method="POST" action="<?= admin_url( "admin-ajax.php" ) ?>" id="code-activator-form">
			<input type="hidden" name="action" value="code_activator">
			<input type="text" name="activate_code" class="input">
			<button class="button button-primary code-activator-button">Активировать</button>
		</form>
		<div class="code-generator-wrap-activator__result">
			<h1 class="success message">Код валиден и активирован</h1>
			<table class="active-info-table">
				<tr>
					<th>Статус</th>
					<th>Тип кода</th>
					<th>Колчичество активация</th>
					<th>Дата публикации</th>
					<th>Автор</th>
				</tr>
				<tr>
					<td class="activate_status">Активирован</td>
					<td class="activate_type">Одноразовый</td>
					<td class="activate_count_active">9</td>
					<td class="activate_date">12.02.2222</td>
					<td class="activate_author">Jeka</td>
				</tr>
			</table>
		</div>
	</div>
</div>