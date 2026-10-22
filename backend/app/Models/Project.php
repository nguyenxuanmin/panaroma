<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Project extends Model
{
    /**
     * Mật khẩu phụ mặc định (owner-only), tự seed khi migrate & khi tạo project mới.
     * Dùng để login xem dự án mà khách hàng (tài khoản admin dùng hằng ngày) không thấy.
     */
    public const SECONDARY_DEFAULT = 'Panaroma@123';

    public function buildings()
    {
        return $this->hasMany(Building::class);
    }

    public function videos()
    {
        return $this->hasMany(Video::class);
    }
}
