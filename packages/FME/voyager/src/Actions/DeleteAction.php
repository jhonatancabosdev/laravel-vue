<?php

namespace TCG\Voyager\Actions;

class DeleteAction extends AbstractAction
{
    public function getTitle()
    {
        return '';
    }

    public function getIcon()
    {
        return 'fas fa-trash-alt';
    }

    public function getPolicy()
    {
        return 'delete';
    }

    public function getAttributes()
    {
        return [
            'class'   => 'btn btn-sm btn-default delete d-flex',
            'data-id' => $this->data->{$this->data->getKeyName()},
            'id'      => 'delete-'.$this->data->{$this->data->getKeyName()},
        ];
    }

    public function getDefaultRoute()
    {
        return 'javascript:;';
    }
}
