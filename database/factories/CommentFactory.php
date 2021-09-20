<?php

namespace Database\Factories;

use App\Models\Comment;
use Illuminate\Database\Eloquent\Factories\Factory;

class CommentFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = Comment::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            'body' => $this->faker->text,
            'user_id' => 'c9246f12-556f-4e96-abc7-1402f3673b59',
            'post_id' => $this->faker->numberBetween($min = 128, $max = 143),
        ];
    }
}
