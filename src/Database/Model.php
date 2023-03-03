<?php

namespace Baubyte\Database;

use Baubyte\Database\Drivers\DatabaseDriver;
use ReflectionClass;

abstract class Model {
    /**
     * Database table.
     *
     * @var string|null
     */
    protected ?string $table = null;

    /**
     * Id and primary key column.
     *
     * @var string
     */
    protected string $primaryKey = "id";

    /**
     * Hidden properties.
     *
     * @var array
     */
    protected array $hidden = [];

    /**
     * Mass assignable attributes.
     *
     * @var array
     */
    protected array $fillable = [];

    /**
     * attributes
     *
     * @var array
     */
    protected array $attributes  = [];

    /**
     * Automatically insert `created_at` and `updated_at` columns.
     *
     * @var boolean
     */
    protected $insertTimestamps = true;
    /**
     * Undocumented variable
     *
     * @var \Baubyte\Database\Drivers\DatabaseDriver|null
     */
    private static ?DatabaseDriver $driver = null;

    /**
     * Set Database Drivers
     *
     * @param \Baubyte\Database\Drivers\DatabaseDriver $driver
     * @return void
     */
    public static function setDatabaseDriver(DatabaseDriver $driver) {
        self::$driver = $driver;
    }
    /**
     * Initialize model.
     */
    public function __construct() {
        if (is_null($this->table)) {
            $subClass = new ReflectionClass(static::class);
            $this->table = snake_case("{$subClass->getShortName()}s");
        }
    }

    /**
     * Mark any property that is being set on this object as a column.
     *
     * @param string $name
     * @param mixed $value
     */
    public function __set($name, $value) {
        $this->attributes[$name] = $value;
    }

    /**
     * Get previously set property.
     *
     * @param $name
     * @return mixed
     */
    public function __get($name) {
        return $this->attributes[$name] ?? null;
    }

    /**
     * Hide properties that shouldn't be serialized.
     *
     * @return array
     */
    public function __sleep() {
        foreach ($this->hidden as $hide) {
            unset($this->attributes[$hide]);
        }
        return array_keys(get_object_vars($this));
    }

    /**
     * Return attributes to array
     *
     * @return array
     */
    public function toArray(): array {
        return array_filter(
            $this->attributes,
            fn ($attribute) => !in_array($attribute, $this->hidden)
        );
    }
    /**
     * Save the current model in the database.
     *
     * @return static
     */
    public function save(): static {
        if ($this->insertTimestamps) {
            $this->attributes["created_at"] = date("Y-m-d H:m:s");
        }
        $databaseColumns = implode(",", array_keys($this->attributes));
        $bind = implode(",", array_fill(0, count($this->attributes), "?"));
        self::$driver->statement(
            "INSERT INTO {$this->table} ({$databaseColumns}) VALUES ({$bind})",
            array_values($this->attributes)
        );

        $this->{$this->primaryKey} = self::$driver->lastInsertId();

        return $this;
    }

    /**
     * Assign all assignable attributes at once.
     *
     * @param array $attributes
     * @return static
     */
    protected function massAssign(array $attributes): static {
        if (count($this->fillable) == 0) {
            throw new \BadMethodCallException("Model " . static::class . " no tiene atributos asignables.");
        }

        foreach ($attributes as $key => $value) {
            if (in_array($key, $this->fillable)) {
                $this->__set($key, $value);
            }
        }

        return $this;
    }

    /**
     * Assign all assignable attributes at once.
     *
     * @param array $attributes
     * @return static
     */
    protected function setAttributes(array $attributes): static {
        foreach ($attributes as $key => $value) {
            $this->__set($key, $value);
        }

        return $this;
    }
    /**
     * Store model in the database.
     *
     * @param array $attributes
     * @return static
     */
    public static function create(array $attributes): static {
        return (new static())->massAssign($attributes)->save();
    }

    /**
     * Update model in the database.
     *
     * @return static
     */
    public function update(): static {
        if ($this->insertTimestamps) {
            $this->attributes["updated_at"] = date("Y-m-d H:m:s");
        }

        $databaseColumns = array_keys($this->attributes);
        $bind = implode(",", array_map(fn ($column) => "$column = ?", $databaseColumns));
        $id = $this->attributes[$this->primaryKey];

        self::$driver->statement(
            "UPDATE $this->table SET $bind WHERE $this->primaryKey = $id",
            array_values($this->attributes)
        );

        return $this;
    }

    /**
     * Delete model in the database.
     *
     * @return static
     */
    public function delete(): static {
        self::$driver->statement(
            "DELETE FROM $this->table WHERE $this->primaryKey = {$this->attributes[$this->primaryKey]}"
        );

        return $this;
    }

    /**
     *  First inserted model.
     *
     * @return static|null
     */
    public static function first(): ?static {
        $model = new static();
        $rows = self::$driver->statement("SELECT * FROM $model->table LIMIT 1");

        if (count($rows) == 0) {
            return null;
        }

        return $model->setAttributes($rows[0]);
    }

    /**
     * Find model with given `$id`.
     *
     * @param integer|string $id
     * @return static|null
     */
    public static function find(int|string $id): ?static {
        $model = new static();
        $rows = self::$driver->statement(
            "SELECT * FROM $model->table WHERE $model->primaryKey = ?",
            [$id]
        );

        if (count($rows) == 0) {
            return null;
        }

        return $model->setAttributes($rows[0]);
    }

    /**
     * Get all the models in the database.
     *
     * @return array
     */
    public static function all(): array {
        $model = new static();
        $rows = self::$driver->statement("SELECT * FROM $model->table");

        if (count($rows) == 0) {
            return [];
        }

        $models = [$model->setAttributes($rows[0])];

        for ($i = 1; $i < count($rows); $i++) {
            $models[] = (new static())->setAttributes($rows[$i]);
        }

        return $models;
    }

    /**
     * Get the models where `$column = $value`
     *
     * @param string $column
     * @param mixed $value
     * @return array
     */
    public static function where(string $column, mixed $value): array {
        $model = new static();
        $rows = self::$driver->statement(
            "SELECT * FROM $model->table WHERE $column = ?",
            [$value]
        );

        if (count($rows) == 0) {
            return [];
        }

        $models = [$model->setAttributes($rows[0])];

        for ($i = 1; $i < count($rows); $i++) {
            $models[] = (new static())->setAttributes($rows[$i]);
        }

        return $models;
    }

    /**
     * Get the first model where `$column = $value`
     *
     * @param string $column
     * @param mixed $value
     * @return static|null
     */
    public static function firstWhere(string $column, mixed $value): ?static {
        $model = new static();
        $rows = self::$driver->statement(
            "SELECT * FROM $model->table WHERE $column = ? LIMIT 1",
            [$value]
        );

        if (count($rows) == 0) {
            return null;
        }

        return $model->setAttributes($rows[0]);
    }
}
