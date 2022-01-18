<?php
namespace cores\traits;
/**
 * 错误信息Trait类
 */
trait ErrorTrait
{
    /**
     * 错误信息
     * @var string
     */
    protected $error = '';

    /**
     * 设置错误信息
     * @param string $error
     * @return void
     */
    protected function setError(string $error): void
    {
        $this->error = $error ?: '未知错误';
    }

    /**
     * 获取错误信息
     * @return string
     */
    public function getError(): string
    {
        return $this->error;
    }

    /**
     * 是否存在错误信息
     * @return bool
     */
    public function hasError(): bool
    {
        return !empty($this->error);
    }
}