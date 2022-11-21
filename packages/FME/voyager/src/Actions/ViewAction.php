<?php

namespace TCG\Voyager\Actions;

class ViewAction extends AbstractAction
{
    public function getTitle()
    {
        return '';
    }

    public function getIcon()
    {
        return 'fas fa-eye';
    }

    public function getPolicy()
    {
        return 'read';
    }

    public function getAttributes()
    {
        return [
            'class' => 'btn btn-sm btn-pink view d-flex',
            'target' => '_blank'
        ];
    }

    public function getDefaultRoute()
    {
        if ($this->dataType->slug === 'products') {
            return route('product.show', $this->data);
        }

        return route('voyager.'.$this->dataType->slug.'.show', $this->data->{$this->data->getKeyName()});
    }
}
