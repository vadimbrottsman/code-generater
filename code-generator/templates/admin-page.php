<?php
    /*
    * Code Generator шаблон страницы генерации кодов
    */
?>
<div class="code-generator-wrap">
    <div class="code-generator-block">
        <h2>Генератор кодов</h2>
        <p>Выберите опцию генерации кодов:  </p>
        <p>Одноразовые коды можно активировать <strong>только 1 раз</strong></p>
        <p>Многоразовые коды <strong>не имеют лимита на активацию</strong></p>
        <p>Можно сгенерировать <strong>не более 100</strong></p>
        <?php 
            if( current_user_can( 'administrator' ) ):
        ?>
            <p>Используйте шорткоды <strong>[code_generator]</strong> - для вывода страницы генерации кода</p>
            <p><strong>[code_generator_list]</strong> - для вывода списка сгенерированных кодов</p>
        <?php
            endif;
        ?>
        <br>
        <div class="code-generator-form">
            <form action="<?= admin_url( "admin-ajax.php" ) ?>" method="POST" id="code-generator-form">
                <input type="hidden" name="action" value="code_generator">
                <table class="code-generator-form-table">
                    <tr valign="top">
                        <th scope="row">Тип кода:</th>
                        <td>
                            <fieldset>
                                <label for="type_one">
                                    <input type="radio" id="type_one" name="type" value="type_one" checked>
                                    <span>Одноразовый</span>
                                </label>
                                <br>
                                <label for="type_no_limit">
                                    <input type="radio" id="type_no_limit" name="type" value="type_no_limit">
                                    <span>Многоразовый</span>
                                </label>
                            </fieldset>
                        </td>
                    </tr>
                    <tr valign="top">
                        <th scope="row">Количество кодов:</th>
                        <td>
                            <input type="number" min="1" max="100" id="count_code" name="count">
                        </td>
                    </tr>
                    <tr valign="top" class="code-generator__error">
                        <td colspan="2">
                            <p class="code-generator__error-message"></p>
                        </td>
                    </tr>
                    <tr valign="top" >
                        <td colspan="2">
                            <button class="code-generator-form__button button button-primary">Генерировать</button>
                        </td>
                    </tr>
                </table>
            </form>
        </div>
    </div>
    <div class="code-generator-block">
        <div class="code-generator-block-answer">
            <h2>Сгенерированные коды:</h2>
            <div class="code-generator-answer">
            
            </div>
            <p>Готово, коды сгенерированы, детальнее можно <a href="/wp-admin/edit.php?post_type=codes">посмотреть тут</a></p>
        </div>
    </div>
</div>