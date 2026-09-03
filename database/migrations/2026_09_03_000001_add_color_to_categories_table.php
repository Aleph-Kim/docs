<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('categories', function (Blueprint $table) {
            $table->string('color', 7)->default('#0f766e')->after('slug');
        });

        // 커스텀 eli5 스킬(DEV_COLOR_RULES) 및 docs-upload 도메인 카테고리 색상 매핑
        $domainRules = [
            '#2563eb' => ['네트워크/통신', '네트워크', '통신', 'network', 'api'],
            '#059669' => ['데이터베이스', '스토리지', '캐시', 'database'],
            '#0891b2' => ['인프라/DevOps', '인프라', '클라우드', 'devops', 'infra'],
            '#d97706' => ['소프트웨어 아키텍처', '아키텍처', 'architecture', '시스템설계', '다이어그램'],
            '#7c3aed' => ['웹/프론트엔드', '프론트엔드', '웹', 'frontend', '인터랙티브', '인공지능/머신러닝', '인공지능', '머신러닝'],
            '#475569' => ['컴퓨터 사이언스', '운영체제', '컴퓨터사이언스'],
            '#4f46e5' => ['보안/인증', '보안', '인증'],
            '#e11d48' => ['테스트/디버깅', '테스트', '디버깅', '실험'],
            '#16a34a' => ['자연과학', '생명공학', '생명공학/바이오', '환경'],
            '#0284c7' => ['물리/우주', '물리', '우주'],
            '#b45309' => ['금융/경제', '금융', '경제'],
            '#be123c' => ['의학/건강', '의학', '건강'],
            '#8b5cf6' => ['인문/사회', '인문', '사회', '역사', '철학', '예술'],
        ];

        $categories = DB::table('categories')->select('id', 'name')->get();
        foreach ($categories as $cat) {
            $nameLower = mb_strtolower($cat->name);
            foreach ($domainRules as $color => $keywords) {
                foreach ($keywords as $kw) {
                    if (str_contains($nameLower, mb_strtolower($kw))) {
                        DB::table('categories')->where('id', $cat->id)->update(['color' => $color]);
                        break 2;
                    }
                }
            }
        }
    }

    public function down(): void
    {
        Schema::table('categories', function (Blueprint $table) {
            $table->dropColumn('color');
        });
    }
};
