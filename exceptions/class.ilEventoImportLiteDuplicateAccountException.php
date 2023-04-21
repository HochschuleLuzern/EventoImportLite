<?php declare(strict_types=1);

class ilEventoImportLiteDuplicateAccountException extends ilException
{
    private string $ext_account_value;
    private array $matching_accounts_list;

    public function __construct(string $ext_account_value, array $matching_account_list, $a_message, $a_code = 0)
    {
        parent::__construct($a_message, $a_code);

        $this->ext_account_value = $ext_account_value;
        $this->matching_accounts_list = $matching_account_list;
    }

    public function getExtAccountValue() : string
    {
        return $this->ext_account_value;
    }

    public function getMatchingAccountsList() : array
    {
        return $this->matching_accounts_list;
    }
}