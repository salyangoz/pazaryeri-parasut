# Pazaryeri -N11, Gittigidiyor, Hepsiburada Paraşüt Entegrasyonu 
Paraşüt ön muhasebe ve fatura programının pazaryerleri ile otomatik entegrasyonunu sağlayan PHP(Laravel) kodudur. Bu paket sayesinde N11, Gittigidiyor ve Hepsiburada satışlarınızı gerekli ayarları yaparak satışlarınızın belirlediğiniz aralıklarda paraşüte işlenmesini sağlar. Eğer e-Fatura veya e-Arşiv müşterisiyseniz paraşüt üzerinde bunların gerekli bilgilere göre e-Arşiv veya e-Fatura olarak gönderilmesini sağlar. Paraşüt'ün http://api.parasut.com/docs bölümündeki Satış Faturaları - Yeni Fatura ve e-Arşiv/e-Fatura kayıt atmasını kullanır. 
## Framework : Laravel

[![Latest Version on Packagist](https://img.shields.io/packagist/v/mayoz/parasut.svg?style=flat-square)](https://packagist.org/packages/salyangoz/parasut-pazaryeri)
[![Software License](https://img.shields.io/badge/license-MIT-brightgreen.svg?style=flat-square)](LICENSE.md)

## Nasıl Yüklenir?

Composer ile yüklemek

``` bash
$ composer require salyangoz/parasut-pazaryeri
```

## Nasıl Kullanılır?


### Paraşüt API Konfigürasyon Süreçleri
1. destek@parasut.com adresine Paraşüt'e kayıtlı olduğunuz e-posta adresinden API kullanmak istediğinizi ve bunun bilgilerini sizinle paylaşılmasını istediğiniz bir e-posta gönderiniz.
2. Gelen bilgilerden Application Id, Secret ve paraşüte giriş yaptığınızda üst linkte bulunan 6 haneyle başlayan numara bölümünü Config/Parasut-Pazaryeri.php dosyasına girin.

#### Pazaryeri paraşüt entegrasyonu pazaryerlerinde kesilen Satış faturalarının otomatik olarak belirleyeceğiniz dakika aralıklarında Paraşüt'e işlenmesi için kullanılır. Paraşüt üzerinden e-Fatura mükellefi olan kullanıcılar bu sistemi kullandığında otomatik olarak Pazaryerlerinde onaylanmış veya kargoya hazır hale getirilmiş durumlarda faturaları kesilir.

### Gittigidiyor API Konfigürasyon Süreçleri
1. http://dev.gittigidiyor.com adresinden mağazanızın bilgileri ile giriş yapın. 
2. Menüden API Anahtarları bölümüne girin.
3. Yeni API anahtarı almak için tıklayın. 
4. Anahtar bilgilerini Config/Parasut-Pazaryeri.php dosyası içindeki bilgilere girin.
5. Daha sonra gittigidiyor üzerinde AUTH_USERNAME ve AUTH_PASSWORD alabilmek için gittigidiyor destek ekibine api.destek@gittigidiyor.com mail adresine mesaj atın. Aşağıda demo mesajı görebilirsiniz. 
6. Gelen bilgileri Config/Parasut-Pazaryeri.php dosyası içindeki bilgilere işleyebilirsiniz.

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
3. Bu bilgileri Config/Parasut-Pazaryeri.php dosyasına girin.

### Hepsiburada Konfigürasyon Süreçleri

1. mpentegrasyon@hepsiburada.com adresine Hepsiburada mağazasına kayıtlı olan e-posta ile Test ve Canlı entegrasyon bilgilerini isteyiniz. 
2. Gönderilen Portal Kullanıcı Adı, Şifre ve Merchant ID bölümlerini Config/Parasut-Pazaryeri.php dosyasına girin.

## Güvenlik

Herhangi bir güvenlik açığı yakalarsanız, issue açmak yerine info@salyangoz.com.tr adresine bildirim yapabilirsiniz.

## Geliştirilme Platformu

* [Laravel](www.laravel.com) - PHP Framework For Web Artisans

## Versiyonlama

* [SemVer](http://semver.org/) versiyonlamayı kullanıyoruz. Versiyonlamaları görebilmek için [tag](https://github.com/salyangoz/pazaryeri-parasut/tags) bölümünü ziyaret edin.

## Katılımcılar

- [Salyangoz Teknoloji](https://github.com/salyangoz)
- [Senol Örencik](https://github.com/yedincisenol)
- [Ece Bitiren](https://github.com/ecuci)

## Lisans

The MIT License (MIT). Please see [License File](LICENSE.md) for more information.

## Ekstralar

[Salyangoz Web Adresi](https://www.salyangoz.com.tr)
