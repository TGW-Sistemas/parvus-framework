<?php
    namespace Parvus;

    class Model extends \Illuminate\Database\Eloquent\Model
    {
        protected $primaryKey = 'id';
        public $timestamps = false;

        public function __construct()
        {

            parent::__construct();
            parent::boot();

            /** Se não tem tabela, recupera pelo nome da classe */
            if ($this->table == NULL)
            {

                $this->table = mb_strtolower(explode("\\",get_called_class())[1],'UTF-8');

            }

            /** When save, remove NULL fields */
            static::saving(function($model)
            {

                foreach ($model->toArray() as $name => $value)
                {

                    if ((string) $value == NULL)
                    {

                        $model->{$name} = null;

                    }

                }

                return true;

            });

        }

        /**
         * Retorna um objeto pelo ID
         * @param $prId
         * @param $prCampo
         * @return mixed
         */
        public final static function getById ($prId,$prCampo = 'id')
        {

            /** Gera o nome da model */
            $model = 'Model\\'.(explode('\\',get_called_class())[1]);

            /** Se existir o model, criar o objeto do model */
            if ($prId != NULL && class_exists($model))
            {

                $value = $model::where($prCampo,$prId)->first();

                if ($value != NULL)
                {

                    return $value;

                }

            }

            return new $model();

        }

        /**
         * Save a array
         * @param $prArray
         */
        public final function saveArray ($prArray)
        {

            foreach ($prArray as $label => $value)
            {

                $this->{$label} = $value;

            }

            $this->save();

        }

    }