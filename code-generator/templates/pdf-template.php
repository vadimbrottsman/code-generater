.code-generator-wrap {
    display: flex;
    justify-content: center;
}

.code-generator-block {
    width: 45%;
}

.code-generator-form-table {
    table-layout: fixed;
}

.code-generator-form-table th,
.code-generator-form-table td {
    padding: 10px;
    width: 50%;
    text-align: left;
}

.code-generator-form__button {
    margin: auto;
}

.code-generator-block-answer {
    display: none;
}

.code-generator__error {
    display: none;
    background-color: #E23A3A;
    color: #fff;
    font-weight: bold;
}

.code-generator__error td {
    text-align: center;
}

@media screen and (max-width: 600px) {
    .code-generator-wrap {
        flex-wrap: wrap;
    }

    .code-generator-block {
        width: 100%;
    }
}