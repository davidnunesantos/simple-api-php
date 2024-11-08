<?php

namespace App\Storage;

class JsonStorage
{
    /**
     * The file where the data is stored.
     */
    private const DATA_FILE = '../data.json';

    /**
     * Loads the data from the file.
     *
     * @return array The loaded data.
     */
    public function load(): array
    {
        // Check if the file exists.
        if (file_exists(self::DATA_FILE)) {
            // Read the file contents.
            $data = file_get_contents(self::DATA_FILE);

            // Decode the JSON and return the data.
            return json_decode($data, true) ?: [];
        }

        // If the file does not exist, return an empty array.
        return [];
    }

    /**
     * Saves the data to the file.
     *
     * @param array $data The data to save.
     * @return void
     */
    public function save(array $data): void
    {
        // Encode the data as JSON.
        $data = json_encode($data);

        // Save the data to the file.
        file_put_contents(self::DATA_FILE, $data);
    }
}
