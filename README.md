Fuzzy
=====
###### Making PocketMine-MP huggable 

Fuzzy is multi-purpose life saving tool for PocketMine-MP. It increases huggability by a factor of 10, guaranteed. 

### What is Fuzzy?
Fuzzy is a PocketMine plugin library to make doing stuff easier. Fuzzy wraps around the PocketMine server and enhances plugin development. Fuzzy includes many standard methods and classes along with a powerful intermediary event system.

### How do I setup Fuzzy?
To activate Fuzzy for your plugin just add the following line to your `plugin.yml`
```yaml
depend: ["FuzzyAPI"]
```
**Note:** If you don't add this, Fuzzy will **not** generate an API object for your plugin, they can't be generated later.

It is recommended that your plugin extend `fuzzy\utils\FuzzyPlugin` instead of `pocketmine\plugin\PluginBase`. Doing so will add a convenience method for accessing the Fuzzy API and add type hinting.

### How do I use Fuzzy?
Fuzzy is available in the `fuzzy` field of your plugin  and can be accessed directly or through `getFuzzy()` (if you plugin extends `FuzzyPlugin`). Your Fuzzy API can be accessed at anytime and doesn't depend on the Fuzzy plugin being enabled.

```php
$this->getFuzzy()->sendFuzz(); // Prints "Fuzzy ❤  PluginName" to console
$this->getFuzzy()->getPlayers(); // Returns a list of online players
```






