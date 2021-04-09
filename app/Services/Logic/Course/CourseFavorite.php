<?php

namespace App\Services\Logic\Course;

use App\Models\Course as CourseModel;
use App\Models\CourseFavorite as CourseFavoriteModel;
use App\Models\User as UserModel;
use App\Repos\CourseFavorite as CourseFavoriteRepo;
use App\Services\Logic\CourseTrait;
use App\Services\Logic\Service as LogicService;
use App\Validators\UserLimit as UserLimitValidator;

class CourseFavorite extends LogicService
{

    use CourseTrait;

    public function handle($id)
    {
        $course = $this->checkCourse($id);

        $user = $this->getLoginUser();

        $validator = new UserLimitValidator();

        $validator->checkFavoriteLimit($user);

        $favoriteRepo = new CourseFavoriteRepo();

        $favorite = $favoriteRepo->findCourseFavorite($course->id, $user->id);

        if (!$favorite) {

            $favorite = new CourseFavoriteModel();

            $favorite->course_id = $course->id;
            $favorite->user_id = $user->id;

            $favorite->create();

            $this->incrCourseFavoriteCount($course);
            $this->incrUserFavoriteCount($user);

        } else {

            $favorite->delete();

            $this->decrCourseFavoriteCount($course);
            $this->decrUserFavoriteCount($user);
        }

        return $course->favorite_count;
    }

    protected function incrCourseFavoriteCount(CourseModel $course)
    {
        $course->favorite_count += 1;

        $course->update();
    }

    protected function decrCourseFavoriteCount(CourseModel $course)
    {
        if ($course->favorite_count > 0) {
            $course->favorite_count -= 1;
            $course->update();
        }
    }

    protected function incrUserFavoriteCount(UserModel $user)
    {
        $user->favorite_count += 1;

        $user->update();
    }

    protected function decrUserFavoriteCount(UserModel $user)
    {
        if ($user->favorite_count > 0) {
            $user->favorite_count -= 1;
            $user->update();
        }
    }

}
