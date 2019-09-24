<?php

class RecursiveDirectory {
    /**
     * 以数组的形式返回某个目录下的所有文件
     *
     * @param $source_dir
     * @param int $directory_depth
     * @param bool $hidden
     * @return array|bool
     */
    public function directoryMap($source_dir, $directory_depth = 0, $hidden = FALSE)
    {
        if ($fp = @opendir($source_dir)) {
            $fileData = array();
            $new_depth = $directory_depth - 1;
            $source_dir = rtrim($source_dir, DIRECTORY_SEPARATOR) . DIRECTORY_SEPARATOR;

            while (FALSE !== ($file = readdir($fp))) {
                // Remove '.', '..', and hidden files [optional]
                if ($file === '.' OR $file === '..' OR ($hidden === FALSE && $file[0] === '.')) {
                    continue;
                }

                is_dir($source_dir . $file) && $file .= DIRECTORY_SEPARATOR;

                if (($directory_depth < 1 OR $new_depth > 0) && is_dir($source_dir . $file)) {
                    $fileData[$file] = $this->directoryMap($source_dir . $file, $new_depth, $hidden);
                } else {
                    $fileData[] = $file;
                }
            }

            closedir($fp);
            return $fileData;
        }

        return false;
    }
}