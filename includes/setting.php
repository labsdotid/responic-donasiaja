<?php

namespace Responic_Donasiaja;


class Setting
{
    private $wpdb;

    private $table;

    private $data = [];

    /**
     * Instance
     */
    private static $_instance = null;

    /**
     * run
     *
     * @return Setting An instance of class
     */
    public static function load()
    {
        if (is_null(self::$_instance)) {
            self::$_instance = new self();
        }
        return self::$_instance;
    }


    /**
     * __construct
     *
     * @return void
     */
    public function __construct()
    {
        global $wpdb;

        $this->wpdb = $wpdb;
        $this->table = $wpdb->prefix . 'dja_settings';

        $this->load_all_setting();
    }

    public function load_all_setting()
    {
        $results = $this->wpdb->get_results("SELECT * FROM {$this->table} LIMIT 100 OFFSET 0");

        $data = [];
        if ($results) {
            foreach ($results as $r) {
                $data[$r->type] = maybe_unserialize($r->data);
            }
        }

        $this->data = $data;
    }

    /**
     * getter
     * @param  string $name [description]
     * @return [type]      [description]
     */
    public function __get($name)
    {
        if (array_key_exists($name, $this->data))
            return maybe_unserialize($this->data[$name]);

        return NULL;
    }

    /**
     * update
     *
     * @param  string $type
     * @param  mixed $data
     * @return mixed
     */
    public function update($type, $data)
    {
        $res = $this->wpdb->update(
            $this->table,
            array('data' => maybe_serialize($data)),
            array('type' => sanitize_text_field($type))
        );

        self::$_instance = null;

        return $res;
    }

    public function reset()
    {
        $this->update('wanotif_url', 'https://api.wanotif.id/v1');
        $this->update('wanotif_apikey', '');
    }
}
