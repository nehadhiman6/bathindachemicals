<?php

namespace App\Http\Controllers\Users;

use App\Http\Controllers\Controller;
use App\Models\Auth\Preference;
use Illuminate\Http\Request;
use Inertia\Inertia;

class UserPreferencesController extends Controller
{

    public function index(Request $request)
    {
        return Inertia::render('ProjectComponents/Users/UserPreferences',[
            // 'sidebar' => userPreferencesSidebar(),
            // 'sidebar_position' => userPreferencesSidebarPosition(),
            // 'color_scheme' => userPreferencesSidebarColorScheme()
        ]);
    }

    public function preferences(Request $request)
    {
        if ($request->ajax()) {
            $user_id = auth()->user()->id;
            $user_preferences = Preference::where('user_id', $user_id)->get();
            return reply(true, [
                'user_preferences' => $user_preferences
            ]);
        }
    }
    public function store(Request $request)
    {
        $user_id = auth()->user()->id;
        if ($request['sidebar']) {
            $sidebar_pref = Preference::firstOrNew(['para_name' => 'sidebar', 'user_id' => $user_id]);
            $sidebar_pref->para_value = $request['sidebar'];
            $sidebar_pref->save();
        }
        if ($request['sidebar_position']) {
            $sidebar_pos_pref = Preference::firstOrNew(['para_name' => 'sidebar_position', 'user_id' => $user_id]);
            $sidebar_pos_pref->para_value = $request['sidebar_position'];
            $sidebar_pos_pref->save();
        }
        if ($request['color_scheme']) {
            $theme_pref = Preference::firstOrNew(['para_name' => 'color_scheme', 'user_id' => $user_id]);
            $theme_pref->para_value = $request['color_scheme'];
            $theme_pref->save();
        }
        if ($request['default_layout']) {
            $theme_pref = Preference::firstOrNew(['para_name' => 'default_layout', 'user_id' => $user_id]);
            $theme_pref->para_value = $request['default_layout'];
            $theme_pref->save();
        }
        return reply(true, [
            // 'preferences' => userPreferences()
        ]);
    }
}
