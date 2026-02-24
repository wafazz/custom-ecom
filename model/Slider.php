<?php

require_once __DIR__ . '/BaseModel.php';

class Slider extends BaseModel
{
    protected $table = 'sliders';

    public function getActive(): array
    {
        return $this->findAll(['status' => 1], 'sort_order ASC');
    }

    public function getAll(): array
    {
        return $this->findAll([], 'sort_order ASC');
    }

    public function deleteSlider(int $id): bool
    {
        $slider = $this->find($id);
        if ($slider && file_exists($slider['image'])) {
            unlink($slider['image']);
        }
        $sql = "DELETE FROM `{$this->table}` WHERE `id` = ?";
        return $this->execute($sql, 'i', [$id]);
    }

    public function toggleStatus(int $id, int $status): bool
    {
        return $this->update($id, ['status' => $status, 'updated_at' => dateNow()]);
    }

    public function updateSortOrder(int $id, int $sortOrder): bool
    {
        return $this->update($id, ['sort_order' => $sortOrder, 'updated_at' => dateNow()]);
    }
}
