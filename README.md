# N11, Gittigidiyor, Hepsiburada "Pazaryeri" Paraşüt Entegrasyonu 
Paraşüt ön muhasebe ve fatura programının pazaryerleri ile otomatik entegrasyonunu sağlayan PHP(Laravel) kodudur. Bu paket sayesinde N11, Gittigidiyor ve Hepsiburada satışlarınızı gerekli ayarları yaparak satışlarınızın belirlediğiniz aralıklarda paraşüte işlenmesini sağlar. Eğer e-Fatura veya e-Arşiv müşterisiyseniz paraşüt üzerinde bunların gerekli bilgilere göre e-Arşiv veya e-Fatura olarak gönderilmesini sağlar. Paraşüt'ün http://api.parasut.com/docs bölümündeki Satış Faturaları - Yeni Fatura ve e-Arşiv/e-Fatura kayıt atmasını kullanır. 
## Framework : Laravel

[![Latest Version on Packagist](https://img.shields.io/packagist/v/mayoz/parasut.svg?style=flat-square)](https://packagist.org/packages/salyangoz/parasut-pazaryeri)
[![Software License](https://img.shields.io/badge/license-MIT-brightgreen.svg?style=flat-square)](LICENSE.md)

## Nasıl Yüklenir?

#### Step: 1

Paket bir Laravel paketi olduğu için öncelikle bir Laravel kurulumunuzun yapılmış olması gerekiyor. [Laravel nasıl kurulur.]( https://laravel.com/docs/5.3/installation)

#### Step: 2 

Paketi yüklemek için Laravel'in yüklü olduğu root klasörde aşağıdaki komutu çalıştırmanız gerekli

``` bash
$ composer require salyangoz/pazaryeri-parasut
```

#### Step: 3

Eklentinin mevcut Laravel'de kullanılabilmesi için Laravel klasörünüzdeki Config/app.php'ye şu değişiklikleri eklemeniz gerekli:

```php
    'providers' => [
        salyangoz\pazaryeriparasut\PazaryeriParasutServiceProvider::class
    ],
```

#### Step: 4

İşlenen siparişlerin tekrar aktarılmaması için, Paraşüte işlenen faturaların kayıt numaralarının tutulduğu Local store olarak kullanılacak `parasut-data.json` dosyasını `storage/app` dizinin içerisine oluşturmanız ve dosyanın yazılabilir olduğundan emin olmanız gerekli.

Linux için örnek komut (Laravel root klasörüündeyken) :
```sh
$ touch storage/app/parasut-data.json
$ chmod 777 storage/app/parasut-data.json
```

#### Step 5: Enviroment ayarlamaları

Paket, proje için kullanılacak N11,Gittigidiyor,Hepsiburada ve Paraşüt bilgilerinizi Laravel projenizdeki `.env` dosyasından alır. Hangi değişkenlerin tanımlanacağını bu repodaki `.env.example` dosyasından bakabilirsiniz.

##### Opsiyonel:

Eğer projeniz için paketteki sabitleri değiştirmeniz gerekirse (ya da paketi geliştirmek isterseniz) config değerleri paket ayarlarını publish etmeniz gerekir bunu yapmak için aşağıdaki komutu kullanabilirsiniz:

``
php artisan vendor:publish --provider="salyangoz\pazaryeriparasut\PazaryeriParasutServiceProvider"
``

Bu komutu çalıştırmanız ardından paketin config.php dosyası Laravel projenizin config dizinine `pazaryeri-parasut.php` olarak kopyalanacaktır ve burda yaptığınız değişiklikler paket içindeki config dosyası ile birleşecek ya da overrite olacaktır.

#### Step 6: Laravel task

Paket siparişleri belirlenen aralıklarla Pazaryeri api servislerine istek yaparak göndermekte. Bunun da gerçekleşmesi için Laravel'e task eklememiz ve zamanlamak gerekli. Bunun için aşağıdaki örneği kullanabilirsiniz.

`App\Console\Kernel.php`

```php

namespace App\Console;

use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;

class Kernel extends ConsoleKernel
{

    protected $commands = [
        \salyangoz\pazaryeriparasut\Commands\Transfer::class
    ];

    /**
     * Define the application's command schedule.
     *
     * @param  \Illuminate\Console\Scheduling\Schedule  $schedule
     * @return void
     */
    protected function schedule(Schedule $schedule)
    {
		      $schedule->command('pazaryeriparasut:transfer')->everyFiveMinutes();
    }

    /**
     * Register the Closure based commands for the application.
     *
     * @return void
     */
    protected function commands()
    {
        require base_path('routes/console.php');
    }
}
```

Önemli Not: Laravel task zamanlayıcının çalışması için Web sunucunuzda cron'un çalışıyor olması gerekli.

[Laravel task zamanlama nasıl tanımlanır](https://laravel.com/docs/5.3/scheduling)

Laravel task zamanlamanın da çalışır halde olduğundan emin olduktan olduğumuzda artık hazırız demektir!

Aşağıdaki Konfigurasyonları da tamamladığınızda, taskın doğru çalışıp çalışmadığını, komut satırından doğrudan çalıştırarak test edebilirsiniz:

``
$ php artisan pazaryeriparasut:transfer
``

## Nasıl Kullanılır?

### Paraşüt API Konfigürasyon Süreçleri
1. destek@parasut.com adresine Paraşüt'e kayıtlı olduğunuz e-posta adresinden API kullanmak istediğinizi ve bunun bilgilerini sizinle paylaşılmasını istediğiniz bir e-posta gönderiniz.
2. Gelen bilgilerden Application Id, Secret ve paraşüte giriş yaptığınızda üst linkte bulunan 6 haneyle başlayan numara bölümünü ve faturalarınız için açacağınız kategori idsini projenizin .env dosyasına girin.

```
PARASUT_CLIENT_ID=
PARASUT_CLIENT_SECRET=
PARASUT_USERNAME=
PARASUT_COMPANY_ID=
PARASUT_PASSWORD=
PARASUT_CATEGORY_ID=
PARASUT_ACCOUNT_ID=
```

#### Pazaryeri paraşüt entegrasyonu pazaryerlerinde kesilen Satış faturalarının otomatik olarak belirleyeceğiniz dakika aralıklarında Paraşüt'e işlenmesi için kullanılır. Paraşüt üzerinden e-Fatura mükellefi olan kullanıcılar bu sistemi kullandığında otomatik olarak Pazaryerlerinde onaylanmış veya kargoya hazır hale getirilmiş durumlarda faturaları kesilir.

### Gittigidiyor API Konfigürasyon Süreçleri
1. http://dev.gittigidiyor.com adresinden mağazanızın bilgileri ile giriş yapın. 
2. Menüden API Anahtarları bölümüne girin.
3. Yeni API anahtarı almak için tıklayın. 
4. Anahtar bilgilerini Config/Parasut-Pazaryeri.php dosyası içindeki bilgilere girin.
5. Daha sonra gittigidiyor üzerinde AUTH_USERNAME ve AUTH_PASSWORD alabilmek için gittigidiyor destek ekibine api.destek@gittigidiyor.com mail adresine mesaj atın. Aşağıda demo mesajı görebilirsiniz. 
6. Gelen bilgileri laravel projenizin .env dosyası içindeki bilgilere işleyebilirsiniz.

```
GITTIGIDIYOR_API_KEY=
GITTIGIDIYOR_SECRET_KEY=
GITTIGIDIYOR_USERNAME=
GITTIGIDIYOR_PASSWORD=
GITTIGIDIYOR_AUTH_USER=
GITTIGIDIYOR_AUTH_PASSWORD=
GITTIGIDIYOR_LANG=tr
```

#### Paraşüt gittigidiyor entegrasyonunun çalışabilmesi için Server veya Hosting ayarlarınızda 8080 ve 8443 portlarının açık olması gerekmektedir.

``` 

API girişinizi yapabilmek için API kullanıcı rolü tanımlaması rica ediyoruz. Aşağıda kullanıcı bilgilerimiz mevcuttur. Mağaza adımız “mağaza-adınız”.  

```

Dilerseniz bu mail sonrası gittigidiyor size bazı bilgiler soracaktır. Bu bilgileri hazır tutmanız için aşağıda bu bilgilerin neler olduğunu paylaşıyoruz.

```
Hesap sahibinin;

Ad:
Soyad:
Mail adresi:
Telefonu:
Firma Ad:
Firma Telefonu:
Firma Web sitesi:
GittiGidiyor Kullanıcı Adı:
Kullanım amacınız ve uygulamanız hakkında detaylı bilgi:

Entegrasyon Firması Adı:
Firma Email Adresi:
Firma Telefonu:
```

### N11 API Konfigürasyon Süreçleri
1. http://so.n11.com adresinden mağazanızın bilgileri ile giriş yapın.
2. Yukarıda bulunan Hesabım - API Hesapları menüsüne girdikten sonra +Yeni Hesap Oluştur buttonuna tıklayın. Bilgilerinizi mağazanızın kayıtlı olduğu e-posta adresinize gelecektir.
3. Bu bilgileri laravel projenizin .env dosyasına girin.

```
N11_APP_KEY=
N11_APP_SECRET=
```

### Hepsiburada Konfigürasyon Süreçleri

1. mpentegrasyon@hepsiburada.com adresine Hepsiburada mağazasına kayıtlı olan e-posta ile Test ve Canlı entegrasyon bilgilerini isteyiniz. 
2. Gönderilen Portal Kullanıcı Adı, Şifre ve Merchant ID bölümlerini laravel projenizdeki .env dosyasına girin.

```
HEPSIBURADA_USERNAME=
HEPSIBURADA_MERCHANT_ID=
HEPSIBURADA_PASSWORD=
```

## Güvenlik

Herhangi bir güvenlik açığı yakalarsanız, issue açmak yerine info@salyangoz.com.tr adresine bildirim yapabilirsiniz.

## Geliştirilme Platformu

* [Laravel](www.laravel.com) - PHP Framework For Web Artisans

## Versiyonlama

* [SemVer](http://semver.org/) versiyonlamayı kullanıyoruz. Versiyonlamaları görebilmek için [tag](https://github.com/salyangoz/pazaryeri-parasut/tags) bölümünü ziyaret edin.

## Katılımcılar

- [Salyangoz Teknoloji](https://github.com/salyangoz)
- [İbrahim Ş. Örencik](https://github.com/yedincisenol)
- [Ece Bitiren](https://github.com/ecuci)

## Lisans

The MIT License (MIT). Please see [License File](LICENSE.md) for more information.

## Ekstralar

[Salyangoz Web Adresi](https://www.salyangoz.com.tr)
