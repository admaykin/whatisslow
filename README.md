# whatisslow
/**
 * This little class records how long it takes each WordPress action or filter
 * to execute which gives a good indicator of what hooks are being slow.
 * You can then debug those hooks to see what hooked functions are causing problems.
 * 
 * This class does NOT time the core WordPress code that is being run between hooks.
 * You could use similar code to this that doesn't have an end processor to do that.
 * 
 * @version 0.4
 * @author Alex Mills (Viper007Bond)
 *
 * This code is released under the same license as WordPress:
 * http://wordpress.org/about/license/
 */
