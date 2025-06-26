<?php

namespace App\Traits;

trait Translatable
{
    /**
     * Get the translation model for the specified language associated with the entity.
     *
     * @param  int  $languageId  The ID of the language.
     */
    public function translation()
    {
        $translationModel = $this->getTranslationModel();

        return $this->hasOne($translationModel)->whereHas('language', function ($query) {
            $query->where('locale', app()->getLocale());
        });
    }

    /**
     * Get all translation models associated with the entity.
     */
    public function translations()
    {
        $translationModel = $this->getTranslationModel();

        return $this->hasMany($translationModel);
    }

    /**
     * Get the translation model associated with the entity.
     *
     * @return string
     */
    protected function translationModel()
    {
        return 'App\Models\\'.class_basename($this).'Translation';
    }
}
