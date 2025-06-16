<?php

namespace App\Neuron\Tools;
use NeuronAI\Tools\Tool;
use NeuronAI\Tools\ToolProperty;
use Illuminate\Support\Facades\DB;

class InsertTestTool extends Tool
{
    // Variable para almacenar el resultado
    protected mixed $result = null;

    public function __construct()
    {
        parent::__construct(
            'insert_test', // Nombre de la herramienta
            'Inserta un nuevo registro en la tabla "test".' // Descripción de la herramienta
        );

        // Definir las propiedades esperadas por la herramienta
        $this->addProperty(
            new ToolProperty(
                name: 'name',
                type: 'string',
                description: 'El nombre del registro.',
                required: true
            )
        );

        $this->addProperty(
            new ToolProperty(
                name: 'description',
                type: 'string',
                description: 'La descripción del registro.',
                required: true
            )
        );

        // Definir la lógica de la herramienta
        $this->setCallable(function (string $name, string $description): void {
            try {
                // Insertar el registro en la tabla "test"
                DB::table('test')->insert([
                    'name' => $name,
                    'description' => $description,
                    'created_at' => now(),
                    'updated_at' => now()
                ]);

                // Almacenar el resultado
                $this->result = [
                    'success' => true,
                    'message' => 'Registro insertado correctamente.',
                    'data' => [
                        'name' => $name,
                        'description' => $description
                    ]
                ];
            } catch (\Exception $e) {
                // Manejar errores y almacenar el resultado
                $this->result = [
                    'success' => false,
                    'message' => 'Error al insertar el registro.',
                    'error' => $e->getMessage()
                ];
            }
        });
    }

    // Método para obtener el resultado de la herramienta
    public function getResult(): mixed
    {
        return $this->result;
    }
}
