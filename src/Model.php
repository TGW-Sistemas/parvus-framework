<?php
    namespace Parvus;

    class Model extends \Illuminate\Database\Eloquent\Model
    {
        protected $primaryKey = 'id';
        public $timestamps = false;

        public function __construct(array $attributes = array())
        {

            /** Se não tem tabela, recupera pelo nome da classe */
            if ($this->table == NULL)
            {

                $this->table = mb_strtolower(explode("\\",get_called_class())[1],'UTF-8');

            }

            parent::__construct($attributes);

        }

        /**
         * Keep the Illuminate 4.2 date format on toArray()/toJSON() (13 uses ISO-8601)
         * @param \DateTimeInterface $date
         * @return string
         */
        protected function serializeDate(\DateTimeInterface $date)
        {

            return $date->format('Y-m-d H:i:s');

        }

        /**
         * Register the model events (once per class)
         */
        protected static function booted()
        {

            /** When save, convert empty fields to NULL */
            static::saving(function($model)
            {

                foreach ($model->getAttributes() as $name => $value)
                {

                    if ($value !== NULL && is_scalar($value) && (string) $value === '')
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