<?php

namespace TCG\Voyager\Actions;

class EditAction extends AbstractAction
{
    public function getTitle()
    {
        return '';
    }

    public function getIcon()
    {
        return 'fas fa-edit';
    }

    public function getPolicy()
    {
        return 'edit';
    }

    public function getAttributes()
    {
        return [
            'class' => 'btn btn-sm btn-default edit d-flex',
        ];
    }

    public function getDefaultRoute()
    {
        return route('voyager.'.$this->dataType->slug.'.edit', $this->data->{$this->data->getKeyName()});
    }
}
