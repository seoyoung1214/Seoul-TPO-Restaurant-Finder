-- Initial Data Insertion Script
-- Team: team12
-- Total rows: 1000+ (distributed across tables)

USE team12;

-- Disable foreign key checks for bulk insert
SET FOREIGN_KEY_CHECKS = 0;

-- 1. Insert districts (25 Seoul districts)
INSERT INTO districts (district_name) VALUES
    ('Gangnam-gu'),
    ('Gangdong-gu'),
    ('Gangbuk-gu'),
    ('Gangseo-gu'),
    ('Gwanak-gu'),
    ('Gwangjin-gu'),
    ('Guro-gu'),
    ('Geumcheon-gu'),
    ('Nowon-gu'),
    ('Dobong-gu'),
    ('Dongdaemun-gu'),
    ('Dongjak-gu'),
    ('Mapo-gu'),
    ('Seodaemun-gu'),
    ('Seocho-gu'),
    ('Seongdong-gu'),
    ('Seongbuk-gu'),
    ('Songpa-gu'),
    ('Yangcheon-gu'),
    ('Yeongdeungpo-gu'),
    ('Yongsan-gu'),
    ('Eunpyeong-gu'),
    ('Jongno-gu'),
    ('Jung-gu'),
    ('Jungnang-gu');

-- 2. Insert cuisines (20 types)
INSERT INTO cuisines (cuisine_name) VALUES
    ('Korean'),
    ('Japanese'),
    ('Chinese'),
    ('Western'),
    ('Italian'),
    ('French'),
    ('Thai'),
    ('Vietnamese'),
    ('Indian'),
    ('Mexican'),
    ('BBQ'),
    ('Chicken'),
    ('Pizza'),
    ('Burger'),
    ('Cafe'),
    ('Bakery'),
    ('Seafood'),
    ('Vegetarian'),
    ('Fusion'),
    ('Dessert');

-- 3. Insert occasions (10 types)
INSERT INTO occasions (occasion_name) VALUES
    ('Date'),
    ('Business Meeting'),
    ('Family Gathering'),
    ('Friend Meetup'),
    ('Solo Dining'),
    ('Birthday'),
    ('Anniversary'),
    ('Casual'),
    ('First Date'),
    ('Group Party');

-- 4. Insert time_slots (5 types)
INSERT INTO time_slots (time_of_day) VALUES
    ('breakfast'),
    ('lunch'),
    ('afternoon'),
    ('dinner'),
    ('late_night');

-- 5. Insert users (100 users)
INSERT INTO users (username, password, gender, birth_year) VALUES
    ('kim_minjun', '$2y$10$abcdefghijklmnopqrstuvwxyz123456789', 'M', 1995),
    ('lee_seoyeon', '$2y$10$abcdefghijklmnopqrstuvwxyz123456789', 'F', 1988),
    ('park_jihoon', '$2y$10$abcdefghijklmnopqrstuvwxyz123456789', 'M', 2002),
    ('choi_yuna', '$2y$10$abcdefghijklmnopqrstuvwxyz123456789', 'F', 1975),
    ('jung_siwoo', '$2y$10$abcdefghijklmnopqrstuvwxyz123456789', 'M', 1991),
    ('kang_sooah', '$2y$10$abcdefghijklmnopqrstuvwxyz123456789', 'F', 1998),
    ('yoon_jiwon', '$2y$10$abcdefghijklmnopqrstuvwxyz123456789', 'F', 1985),
    ('jang_hyunwoo', '$2y$10$abcdefghijklmnopqrstuvwxyz123456789', 'M', 1968),
    ('lim_chaeyoung', '$2y$10$abcdefghijklmnopqrstuvwxyz123456789', 'F', 2005),
    ('han_minseok', '$2y$10$abcdefghijklmnopqrstuvwxyz123456789', 'M', 1982),
    ('shin_yujin', '$2y$10$abcdefghijklmnopqrstuvwxyz123456789', 'F', 2008),
    ('oh_taehyun', '$2y$10$abcdefghijklmnopqrstuvwxyz123456789', 'M', 1963),
    ('seo_eunji', '$2y$10$abcdefghijklmnopqrstuvwxyz123456789', 'F', 1993),
    ('ahn_donghyun', '$2y$10$abcdefghijklmnopqrstuvwxyz123456789', 'M', 1979),
    ('song_nayeon', '$2y$10$abcdefghijklmnopqrstuvwxyz123456789', 'F', 1996),
    ('hong_jaehyun', '$2y$10$abcdefghijklmnopqrstuvwxyz123456789', 'M', 2001),
    ('moon_sohee', '$2y$10$abcdefghijklmnopqrstuvwxyz123456789', 'F', 1971),
    ('nam_kyungsoo', '$2y$10$abcdefghijklmnopqrstuvwxyz123456789', 'M', 1987),
    ('kwon_jisoo', '$2y$10$abcdefghijklmnopqrstuvwxyz123456789', 'F', 2004),
    ('bae_seungho', '$2y$10$abcdefghijklmnopqrstuvwxyz123456789', 'M', 1976),
    ('go_hyejin', '$2y$10$abcdefghijklmnopqrstuvwxyz123456789', 'F', 1999),
    ('jo_minho', '$2y$10$abcdefghijklmnopqrstuvwxyz123456789', 'M', 1965),
    ('yu_areum', '$2y$10$abcdefghijklmnopqrstuvwxyz123456789', 'F', 1992),
    ('noh_jungwoo', '$2y$10$abcdefghijklmnopqrstuvwxyz123456789', 'M', 1984),
    ('woo_dahyun', '$2y$10$abcdefghijklmnopqrstuvwxyz123456789', 'F', 2007),
    ('son_joonho', '$2y$10$abcdefghijklmnopqrstuvwxyz123456789', 'M', 1960),
    ('baek_sooyoung', '$2y$10$abcdefghijklmnopqrstuvwxyz123456789', 'F', 1989),
    ('heo_sanghoon', '$2y$10$abcdefghijklmnopqrstuvwxyz123456789', 'M', 1973),
    ('yang_minji', '$2y$10$abcdefghijklmnopqrstuvwxyz123456789', 'F', 2010),
    ('im_youngjae', '$2y$10$abcdefghijklmnopqrstuvwxyz123456789', 'M', 1997),
    ('pyo_seoyoung', '$2y$10$abcdefghijklmnopqrstuvwxyz123456789', 'F', 1978),
    ('ki_dohyun', '$2y$10$abcdefghijklmnopqrstuvwxyz123456789', 'M', 2003),
    ('ryoo_jiyoung', '$2y$10$abcdefghijklmnopqrstuvwxyz123456789', 'F', 1966),
    ('ma_junseok', '$2y$10$abcdefghijklmnopqrstuvwxyz123456789', 'M', 1994),
    ('bang_hayoung', '$2y$10$abcdefghijklmnopqrstuvwxyz123456789', 'F', 1981),
    ('seok_jihwan', '$2y$10$abcdefghijklmnopqrstuvwxyz123456789', 'M', 1969),
    ('myung_yebin', '$2y$10$abcdefghijklmnopqrstuvwxyz123456789', 'F', 2006),
    ('ko_seungjin', '$2y$10$abcdefghijklmnopqrstuvwxyz123456789', 'M', 1990),
    ('tan_hyewon', '$2y$10$abcdefghijklmnopqrstuvwxyz123456789', 'F', 1974),
    ('hwang_minsoo', '$2y$10$abcdefghijklmnopqrstuvwxyz123456789', 'M', 2000),
    ('yoo_subin', '$2y$10$abcdefghijklmnopqrstuvwxyz123456789', 'F', 1986),
    ('chang_wonshik', '$2y$10$abcdefghijklmnopqrstuvwxyz123456789', 'M', 1962),
    ('cheon_gayoung', '$2y$10$abcdefghijklmnopqrstuvwxyz123456789', 'F', 2009),
    ('sim_doyoon', '$2y$10$abcdefghijklmnopqrstuvwxyz123456789', 'M', 1983),
    ('cha_seohyun', '$2y$10$abcdefghijklmnopqrstuvwxyz123456789', 'F', 1977),
    ('ku_jinwoo', '$2y$10$abcdefghijklmnopqrstuvwxyz123456789', 'M', 1995),
    ('soon_jiwoo', '$2y$10$abcdefghijklmnopqrstuvwxyz123456789', 'F', 2001),
    ('bin_changmin', '$2y$10$abcdefghijklmnopqrstuvwxyz123456789', 'M', 1967),
    ('jeon_bokyung', '$2y$10$abcdefghijklmnopqrstuvwxyz123456789', 'F', 1991),
    ('won_jaewon', '$2y$10$abcdefghijklmnopqrstuvwxyz123456789', 'M', 1980),
    ('gi_younghee', '$2y$10$abcdefghijklmnopqrstuvwxyz123456789', 'F', 2004),
    ('on_kyungmin', '$2y$10$abcdefghijklmnopqrstuvwxyz123456789', 'M', 1972),
    ('tak_jieun', '$2y$10$abcdefghijklmnopqrstuvwxyz123456789', 'F', 1998),
    ('pin_sungjin', '$2y$10$abcdefghijklmnopqrstuvwxyz123456789', 'M', 2008),
    ('geum_aerin', '$2y$10$abcdefghijklmnopqrstuvwxyz123456789', 'F', 1964),
    ('ha_gunwoo', '$2y$10$abcdefghijklmnopqrstuvwxyz123456789', 'M', 1993),
    ('ryu_jinsol', '$2y$10$abcdefghijklmnopqrstuvwxyz123456789', 'F', 1985),
    ('mo_hanbit', '$2y$10$abcdefghijklmnopqrstuvwxyz123456789', 'M', 1970),
    ('um_sarang', '$2y$10$abcdefghijklmnopqrstuvwxyz123456789', 'F', 2007),
    ('gang_taewook', '$2y$10$abcdefghijklmnopqrstuvwxyz123456789', 'M', 1988),
    ('je_sohyeon', '$2y$10$abcdefghijklmnopqrstuvwxyz123456789', 'F', 1975),
    ('huh_seungwoo', '$2y$10$abcdefghijklmnopqrstuvwxyz123456789', 'M', 2002),
    ('gong_seulgi', '$2y$10$abcdefghijklmnopqrstuvwxyz123456789', 'F', 1989),
    ('sun_youngmin', '$2y$10$abcdefghijklmnopqrstuvwxyz123456789', 'M', 1961),
    ('ji_chaewon', '$2y$10$abcdefghijklmnopqrstuvwxyz123456789', 'F', 2005),
    ('do_jongseok', '$2y$10$abcdefghijklmnopqrstuvwxyz123456789', 'M', 1979),
    ('sang_mikyung', '$2y$10$abcdefghijklmnopqrstuvwxyz123456789', 'F', 1996),
    ('so_kyungho', '$2y$10$abcdefghijklmnopqrstuvwxyz123456789', 'M', 1982),
    ('man_jiyeon', '$2y$10$abcdefghijklmnopqrstuvwxyz123456789', 'F', 2010),
    ('pi_donghoon', '$2y$10$abcdefghijklmnopqrstuvwxyz123456789', 'M', 1968),
    ('dang_yerin', '$2y$10$abcdefghijklmnopqrstuvwxyz123456789', 'F', 1994),
    ('gal_sungmin', '$2y$10$abcdefghijklmnopqrstuvwxyz123456789', 'M', 1976),
    ('sa_hyunjung', '$2y$10$abcdefghijklmnopqrstuvwxyz123456789', 'F', 2009),
    ('tae_woojin', '$2y$10$abcdefghijklmnopqrstuvwxyz123456789', 'M', 1987),
    ('gan_suyeon', '$2y$10$abcdefghijklmnopqrstuvwxyz123456789', 'F', 1973),
    ('du_yoonsung', '$2y$10$abcdefghijklmnopqrstuvwxyz123456789', 'M', 2003),
    ('gu_hyerim', '$2y$10$abcdefghijklmnopqrstuvwxyz123456789', 'F', 1963),
    ('ga_seunghyun', '$2y$10$abcdefghijklmnopqrstuvwxyz123456789', 'M', 1992),
    ('na_boram', '$2y$10$abcdefghijklmnopqrstuvwxyz123456789', 'F', 1984),
    ('da_junghoon', '$2y$10$abcdefghijklmnopqrstuvwxyz123456789', 'M', 1965),
    ('la_seoyun', '$2y$10$abcdefghijklmnopqrstuvwxyz123456789', 'F', 2006),
    ('ma_hyunsung', '$2y$10$abcdefghijklmnopqrstuvwxyz123456789', 'M', 1990),
    ('ba_yunhee', '$2y$10$abcdefghijklmnopqrstuvwxyz123456789', 'F', 1978),
    ('sa_joonwoo', '$2y$10$abcdefghijklmnopqrstuvwxyz123456789', 'M', 2001),
    ('ah_soeun', '$2y$10$abcdefghijklmnopqrstuvwxyz123456789', 'F', 1986),
    ('ja_youngchul', '$2y$10$abcdefghijklmnopqrstuvwxyz123456789', 'M', 1960),
    ('cha_jimin', '$2y$10$abcdefghijklmnopqrstuvwxyz123456789', 'F', 2008),
    ('ka_sunwoo', '$2y$10$abcdefghijklmnopqrstuvwxyz123456789', 'M', 1974),
    ('ta_nayoung', '$2y$10$abcdefghijklmnopqrstuvwxyz123456789', 'F', 1997),
    ('pa_gunho', '$2y$10$abcdefghijklmnopqrstuvwxyz123456789', 'M', 1981),
    ('ha_jieun', '$2y$10$abcdefghijklmnopqrstuvwxyz123456789', 'F', 2004),
    ('bu_sangwoo', '$2y$10$abcdefghijklmnopqrstuvwxyz123456789', 'M', 1969),
    ('su_yeonhee', '$2y$10$abcdefghijklmnopqrstuvwxyz123456789', 'F', 1995),
    ('nu_jungho', '$2y$10$abcdefghijklmnopqrstuvwxyz123456789', 'M', 1983),
    ('du_ahreum', '$2y$10$abcdefghijklmnopqrstuvwxyz123456789', 'F', 2010),
    ('ru_minjae', '$2y$10$abcdefghijklmnopqrstuvwxyz123456789', 'M', 1988),
    ('kim_seoyoung', '$2y$10$abcdefghijklmnopqrstuvwxyz123456789', 'F', 2002),
    ('lee_nakyeong', '$2y$10$abcdefghijklmnopqrstuvwxyz123456789', 'F', 2003),
    ('park_nadam', '$2y$10$abcdefghijklmnopqrstuvwxyz123456789', 'F', 2001),
    ('yang_dongseon', '$2y$10$abcdefghijklmnopqrstuvwxyz123456789', 'F', 2002);

-- 6. Insert restaurants (100 restaurants)
INSERT INTO restaurants (name, district_id, address, description, price, opening_hours, closed_day) VALUES
    ('Mingles', 1, '19 Dosan-daero 67-gil, Gangnam-gu', 'Modern Korean fine dining with seasonal ingredients', 150000, '18:00-22:00', 'Sunday'),
    ('Jungsik', 1, '11 Seolleung-ro 158-gil, Gangnam-gu', 'Contemporary Korean cuisine', 180000, '12:00-22:00', 'Monday'),
    ('Maple Tree House', 1, '136 Dosan-daero, Gangnam-gu', 'Premium Korean BBQ restaurant', 65000, '11:30-23:00', NULL),
    ('Tosokchon', 23, '5 Jahamun-ro 5-gil, Jongno-gu', 'Famous for traditional ginseng chicken soup', 22000, '10:00-22:00', 'Tuesday'),
    ('Gwangjang Market Bindaetteok', 24, '88 Changgyeonggung-ro, Jung-gu', 'Traditional mung bean pancake stall', 8000, '09:00-23:00', NULL),
    ('Hanilkwan', 24, '40 Cheonggyecheon-ro, Jung-gu', 'Historic Korean restaurant since 1939', 45000, '11:00-21:30', NULL),
    ('Gaehwaok', 24, '10 Ujeongguk-ro, Jung-gu', 'Traditional Korean restaurant specializing in gomtang', 15000, '00:00-23:59', NULL),
    ('Bicena', 24, '18 Toegye-ro, Jung-gu', 'Michelin-starred Korean restaurant', 120000, '12:00-21:30', 'Sunday'),
    ('Balwoo Gongyang', 23, '56 Ujeongguk-ro, Jongno-gu', 'Korean Buddhist temple cuisine', 65000, '12:00-21:00', 'Monday'),
    ('Mugyodong Bugeokukjib', 24, '52-11 Myeongdong 8na-gil, Jung-gu', 'Dried pollack soup specialty', 11000, '08:00-22:00', NULL),
    ('La Yeon', 24, '125 Sogong-ro, Jung-gu', 'Three Michelin star Korean restaurant', 200000, '12:00-21:30', NULL),
    ('Samwon Garden', 1, '623 Eonju-ro, Gangnam-gu', 'Upscale Korean BBQ with beautiful garden', 85000, '11:30-22:00', NULL),
    ('Wangbijib', 13, '56 Sinchon-ro, Mapo-gu', 'Affordable pork cutlet restaurant popular with students', 9000, '11:00-22:00', NULL),
    ('Onsaemiro', 13, '155 Yanghwa-ro, Mapo-gu', 'Cozy samgyeopsal restaurant in Hongdae', 14000, '17:00-03:00', NULL),
    ('Yeontabal', 13, '22 Wausan-ro 29ma-gil, Mapo-gu', 'Charcoal-grilled BBQ restaurant', 17000, '17:00-02:00', NULL),
    ('Mad for Garlic', 1, '452 Apgujeong-ro, Gangnam-gu', 'Italian fusion with garlic theme', 28000, '11:30-22:00', NULL),
    ('School Food', 13, '20 Hongik-ro 3-gil, Mapo-gu', 'Modern Korean street food', 13000, '11:00-22:00', NULL),
    ('Passion 5', 13, '272 Itaewon-ro, Yongsan-gu', 'Premium dessert cafe and bakery', 18000, '10:00-22:00', NULL),
    ('Sushi Cho', 1, '11 Eonju-ro 170-gil, Gangnam-gu', 'High-end omakase sushi', 250000, '18:00-22:00', 'Sunday'),
    ('Noryangjin Fish Market', 12, '674 Nodeul-ro, Dongjak-gu', 'Fresh seafood market with restaurants', 45000, '00:00-23:59', NULL),
    ('Hanchu', 21, '216 Itaewon-ro, Yongsan-gu', 'Premium Korean beef restaurant', 95000, '12:00-22:00', NULL),
    ('Poom Seoul', 23, '76 Yulgok-ro, Jongno-gu', 'Modern Korean dining with traditional touches', 75000, '12:00-21:30', 'Monday'),
    ('Jaha Sonmandoo', 23, '10-5 Jahamun-ro 7-gil, Jongno-gu', 'Hand-made dumpling restaurant', 12000, '11:00-21:00', 'Sunday'),
    ('Imun Seolnongtang', 23, '38-13 Yulgok-ro 3-gil, Jongno-gu', 'Traditional ox bone soup', 13000, '00:00-23:59', NULL),
    ('Tteuran Sushi', 13, '12 Hongik-ro 5-gil, Mapo-gu', 'Affordable conveyor belt sushi', 15000, '11:30-22:00', NULL),
    ('Vatos Urban Tacos', 21, '231 Itaewon-ro, Yongsan-gu', 'Korean-Mexican fusion tacos', 22000, '11:30-23:00', NULL),
    ('Egg Drop', 1, '411 Gangnam-daero, Gangnam-gu', 'Popular egg sandwich chain', 7500, '08:00-21:00', NULL),
    ('Sulbing', 13, '90 Eoulmadang-ro, Mapo-gu', 'Korean shaved ice dessert cafe', 13000, '11:00-23:00', NULL),
    ('Anthracite Coffee', 13, '240-1 Seongsan-dong, Mapo-gu', 'Industrial-style specialty coffee roastery', 6500, '10:00-22:00', NULL),
    ('Blue Bottle Coffee Samcheong', 23, '8 Bukchon-ro 5-gil, Jongno-gu', 'Specialty coffee in traditional neighborhood', 7000, '08:00-19:00', NULL),
    ('Plant Cafe', 21, '14 Sowol-ro 2-gil, Yongsan-gu', 'Vegan and vegetarian restaurant', 16000, '11:00-21:00', 'Monday'),
    ('Jungsik Dang', 1, '11 Seolleung-ro 158-gil, Gangnam-gu', 'Casual dining by Jungsik', 45000, '11:30-21:30', NULL),
    ('Born and Bred', 1, '21 Seolleung-ro 151-gil, Gangnam-gu', 'Premium Korean beef BBQ', 110000, '17:00-23:00', 'Sunday'),
    ('Bongpiyang', 13, '22 Wausan-ro 21-gil, Mapo-gu', 'Authentic French bistro', 38000, '18:00-23:00', 'Monday'),
    ('Gorilla Brewing', 13, '31 Wausan-ro 27-gil, Mapo-gu', 'Craft beer pub with burgers', 24000, '17:00-02:00', NULL),
    ('Masizzim', 1, '423 Gangnam-daero, Gangnam-gu', 'Spicy chicken stew chain', 11000, '11:00-23:00', NULL),
    ('Myeongdong Kyoja', 24, '29 Myeongdong 10-gil, Jung-gu', 'Famous kalguksu and mandu', 10000, '10:30-21:30', 'Sunday'),
    ('Gogung', 24, '27 Myeongdong 9-gil, Jung-gu', 'Bibimbap specialty restaurant', 13000, '11:00-22:00', NULL),
    ('Hadongkwan', 24, '12 Myeongdong 9-gil, Jung-gu', 'Traditional gomtang since 1939', 15000, '07:00-16:30', 'Sunday'),
    ('Jinokhwa Halmae Wonjo Dakhanmari', 11, '32-1 Dongmun-ro, Dongdaemun-gu', 'Chicken soup specialty', 14000, '10:00-01:00', NULL),
    ('Ddongchimi Naengmyeon', 23, '136-1 Gwanhun-dong, Jongno-gu', 'Traditional cold noodles', 11000, '11:30-21:00', 'Monday'),
    ('Bukchon Son Mandu', 23, '10-11 Bukchon-ro 5-gil, Jongno-gu', 'Handmade dumpling restaurant', 9000, '11:00-21:00', NULL),
    ('Samcheongdong Sujebi', 23, '101-1 Samcheong-ro, Jongno-gu', 'Korean hand-pulled dough soup', 8000, '10:30-21:00', 'Tuesday'),
    ('Hanchu Korean Beef BBQ', 21, '216 Itaewon-ro, Yongsan-gu', 'Premium hanwoo beef restaurant', 98000, '12:00-22:00', NULL),
    ('Maple Tree House Itaewon', 21, '46 Sinheung-ro, Yongsan-gu', 'Korean BBQ near Itaewon station', 62000, '11:30-23:00', NULL),
    ('The Place Dining', 21, '8 Itaewon-ro 54-gil, Yongsan-gu', 'Steak and Western cuisine', 55000, '17:00-23:00', 'Monday'),
    ('Noksapyeong Galbi', 21, '119 Daesagwan-ro, Yongsan-gu', 'Charcoal grilled galbi restaurant', 48000, '11:00-22:00', NULL),
    ('Seoga and Cook', 13, '1 Hongik-ro 7-gil, Mapo-gu', 'Italian restaurant in Hongdae', 32000, '12:00-22:00', 'Sunday'),
    ('Thanks Oatmeal', 13, '23 Eoulmadang-ro 5-gil, Mapo-gu', 'Healthy oatmeal cafe', 9500, '09:00-20:00', NULL),
    ('Fritz Coffee Company', 13, '20 Wausan-ro 29ra-gil, Mapo-gu', 'Specialty coffee roastery', 6500, '10:00-22:00', NULL),
    ('Cafe Onion Seongsu', 16, '8 Achasan-ro 9-gil, Seongdong-gu', 'Industrial bakery cafe', 8500, '08:00-22:00', NULL),
    ('Daelim Changgo', 13, '42 Yanghwa-ro 7-gil, Mapo-gu', 'Renovated warehouse cafe and gallery', 7000, '11:00-22:00', NULL),
    ('Osegyehyang', 23, '14-5 Insadong 10-gil, Jongno-gu', 'Vegetarian Buddhist cuisine', 25000, '11:30-21:00', 'Sunday'),
    ('Tuk Tuk Noodle Thai', 21, '34 Noksapyeong-daero 26-gil, Yongsan-gu', 'Authentic Thai street food', 14000, '11:30-22:00', NULL),
    ('Saemaeul Sikdang', 1, '567 Gangnam-daero, Gangnam-gu', 'Spicy grilled pork chain', 10000, '00:00-23:59', NULL),
    ('YG Republique', 1, '416 Apgujeong-ro, Gangnam-gu', 'BBQ restaurant by YG Entertainment', 45000, '11:30-23:00', NULL),
    ('Soban Korea', 23, '18 Bukchon-ro 5ga-gil, Jongno-gu', 'Traditional Korean set meal', 28000, '11:00-21:00', 'Monday'),
    ('Gwangjang Market Yukhoe', 24, '88 Changgyeonggung-ro, Jung-gu', 'Raw beef specialty stall', 12000, '09:00-22:00', NULL),
    ('Cafe Layered Jongno', 23, '32 Insadong-gil, Jongno-gu', 'Multi-story dessert cafe', 15000, '10:00-23:00', NULL),
    ('Line Friends Cafe Itaewon', 21, '200 Itaewon-ro, Yongsan-gu', 'Character-themed cafe', 12000, '10:00-22:00', NULL),
    ('Innisfree Jeju House', 13, '60 Hongik-ro 3-gil, Mapo-gu', 'Jeju-themed cafe by cosmetics brand', 9000, '10:00-21:00', NULL),
    ('Ikseon-dong Hanok Cafe', 23, '11 Supyo-ro 28-gil, Jongno-gu', 'Traditional hanok tea house', 10000, '11:00-21:00', NULL),
    ('Mango Six', 1, '445 Gangnam-daero, Gangnam-gu', 'Mango dessert cafe chain', 11000, '11:00-23:00', NULL),
    ('Mom''s Touch', 14, '44 Yonsei-ro, Seodaemun-gu', 'Korean fried chicken burger chain', 7500, '10:00-23:00', NULL),
    ('Shake Shack Gangnam', 1, '435 Gangnam-daero, Gangnam-gu', 'American burger chain', 12000, '11:00-23:00', NULL),
    ('Ryunique', 1, '21 Hakdong-ro 4-gil, Gangnam-gu', 'Creative contemporary Korean cuisine', 95000, '18:00-22:00', 'Sunday'),
    ('Tavolo 24', 21, '95 Cheongpa-ro 20-gil, Yongsan-gu', 'Italian restaurant with wine bar', 42000, '11:30-22:30', NULL),
    ('Pyeongyang Myeonok', 24, '26 Jayang-dong, Jung-gu', 'North Korean style naengmyeon', 13000, '11:30-21:00', 'Monday'),
    ('Woo Lae Oak', 13, '62-29 Changgyeonggung-ro, Mapo-gu', 'Historic Korean BBQ since 1946', 38000, '11:30-22:00', NULL),
    ('Dongdaemun Yukgaejang', 11, '272 Jangchungdan-ro, Dongdaemun-gu', 'Spicy beef soup specialty', 10000, '00:00-23:59', NULL),
    ('Gwangjang Market Mayak Gimbap', 24, '88 Changgyeonggung-ro, Jung-gu', 'Addictive mini gimbap', 4000, '08:00-22:00', NULL),
    ('Tongin Market Dosirak Cafe', 23, '18 Jahamun-ro 15-gil, Jongno-gu', 'Lunch box cafe with coin system', 6500, '08:00-16:00', 'Monday'),
    ('Noryangjin Sashimi Alley', 12, '674 Nodeul-ro, Dongjak-gu', 'Fresh fish market restaurants', 50000, '00:00-23:59', NULL),
    ('Starbucks Reserve Jongno', 23, '20 Jongno 2-gil, Jongno-gu', 'Premium Starbucks Reserve', 9000, '07:00-22:00', NULL),
    ('Terarosa Coffee Gangnam', 1, '11 Hakdong-ro 30-gil, Gangnam-gu', 'Specialty coffee roastery', 7500, '08:00-22:00', NULL),
    ('Dokebi Korean Restaurant', 13, '19 Eoulmadang-ro 5-gil, Mapo-gu', 'Modern Korean fusion restaurant', 32000, '11:30-22:00', 'Sunday'),
    ('Mapo Jeukseok Tteokbokki', 13, '42 Mapo-daero, Mapo-gu', 'Instant tteokbokki specialty', 5500, '10:00-23:00', NULL),
    ('Hangang Ramen', 13, '234 Mapo-daero, Mapo-gu', 'Late-night instant ramen restaurant', 6000, '16:00-05:00', NULL),
    ('Myeongdong Dakgalbi Golmok', 24, '21 Myeongdong 8-gil, Jung-gu', 'Spicy stir-fried chicken', 11000, '11:00-23:00', NULL),
    ('Buam-dong Pig''s Feet', 23, '117 Jaha-mun-ro, Jongno-gu', 'Traditional pig''s feet restaurant', 28000, '16:00-02:00', NULL),
    ('Itaewon Kebab', 21, '149 Itaewon-ro, Yongsan-gu', 'Middle Eastern kebab restaurant', 10000, '11:00-04:00', NULL),
    ('Linus BBQ Gangnam', 1, '518 Teheran-ro, Gangnam-gu', 'American BBQ restaurant', 38000, '11:30-22:00', 'Monday'),
    ('Kitchen Garden Gangnam', 1, '31 Bongeunsa-ro 2-gil, Gangnam-gu', 'Healthy salad and bowl restaurant', 14000, '08:00-21:00', NULL),
    ('Tartine Gangnam', 1, '407 Apgujeong-ro, Gangnam-gu', 'French bakery and cafe', 18000, '08:00-21:00', NULL),
    ('Seokparang', 23, '309 Jahamun-ro, Jongno-gu', 'Fine dining with hanok views', 88000, '12:00-21:00', 'Sunday'),
    ('Muoki', 18, '5 Olympic-ro 35da-gil, Songpa-gu', 'Japanese fusion restaurant', 42000, '11:30-22:00', 'Monday'),
    ('Hanilkwan Apgujeong', 1, '645 Sinsa-dong, Gangnam-gu', 'Branch of historic Korean restaurant', 48000, '11:00-21:30', NULL),
    ('Gaon', 1, '317 Dosan-daero, Gangnam-gu', 'Three Michelin star Korean restaurant', 220000, '12:00-21:30', 'Sunday'),
    ('Soigné', 1, '459 Dosan-daero, Gangnam-gu', 'Contemporary fine dining', 165000, '18:30-22:00', 'Sunday'),
    ('Stay', 13, '27 Wausan-ro 29ma-gil, Mapo-gu', 'Modern European restaurant', 52000, '18:00-23:00', 'Monday'),
    ('Jinmi Pyeongyang Naengmyeon', 24, '18-4 Eulji-ro 12-gil, Jung-gu', 'North Korean style cold noodles', 14000, '11:00-21:00', NULL),
    ('Oegojip Seolleongtang', 13, '32 Yonsei-ro 5da-gil, Mapo-gu', 'Traditional ox bone soup', 11000, '00:00-23:59', NULL),
    ('Pildong Myeonok', 24, '26 Seoae-ro, Jung-gu', 'Old-school naengmyeon restaurant', 12000, '11:00-21:00', 'Sunday'),
    ('Allen Sushi Bar', 21, '217 Itaewon-ro, Yongsan-gu', 'Casual omakase sushi', 85000, '18:00-22:30', 'Monday'),
    ('Maple Tree House Yeouido', 20, '36 Gukjegeumyung-ro 8-gil, Yeongdeungpo-gu', 'Korean BBQ near business district', 68000, '11:30-23:00', NULL),
    ('Black Pig Jeju', 1, '614 Gangnam-daero, Gangnam-gu', 'Jeju black pork BBQ', 42000, '11:00-23:00', NULL),
    ('Congdu Pork Belly', 13, '34 Yanghwa-ro 16-gil, Mapo-gu', 'Premium pork belly restaurant', 18000, '17:00-02:00', NULL),
    ('Seoul Samgyetang', 23, '85-1 Samil-daero 30-gil, Jongno-gu', 'Ginseng chicken soup specialty', 16000, '10:00-22:00', 'Tuesday'),
    ('Dongbaekseom', 1, '25 Apgujeong-ro 10-gil, Gangnam-gu', 'Authentic Korean seafood restaurant with fresh catch daily', 72000, '11:00-22:00', NULL),
    ('Le Chamber Seoul', 13, '45 Wausan-ro 21-gil, Mapo-gu', 'French fine dining with Korean seasonal ingredients', 135000, '18:00-22:30', 'Monday'),
    ('Kimchi Stew Galbi', 5, '101 Dobong-ro, Gangbuk-gu', 'Traditional kimchi stew and grilled ribs', 16000, '10:00-21:00', 'Monday'),
    ('Hongdae California Pizza', 13, '222 Hongdae-ro, Mapo-gu', 'Casual American-style pizza house', 21000, '11:00-23:00', NULL),
    ('Mapo Chicken Story', 13, '333 Mapo-daero, Mapo-gu', 'Crispy Korean fried chicken', 19000, '12:00-24:00', 'Wednesday'),
    ('Cheongdam Bossam', 1, '9-1 Apgujeong-ro, Gangnam-gu', 'Signature bossam and side dishes', 28000, '10:30-22:30', 'Thursday'),
    ('Jongno Gamjatang', 23, '87 Jongno 1-ga, Jongno-gu', 'Pork backbone stew, open late night', 12000, '09:00-03:00', NULL),
    ('The Soban Itaewon', 21, '2 Itaewon-ro 42-gil, Yongsan-gu', 'Modern Korean fusion grill', 37000, '17:00-24:00', NULL),
    ('Yeoksam Cafe Moment', 1, '98 Teheran-ro, Gangnam-gu', 'Trendy dessert and brunch cafe', 17000, '07:00-22:00', NULL),
    ('Gwangjin Sushi Garden', 6, '54 Wangsimni-ro, Gwangjin-gu', 'Japanese sushi & sake bar', 38000, '12:00-22:00', NULL),
    ('Seodaemun Tteokbokki', 14, '11 Yonsei-ro 15-gil, Seodaemun-gu', 'Spicy rice cakes and tempura', 9000, '11:00-21:00', NULL),
    ('Cheese Dakgalbi', 18, '610 Olympic-ro, Songpa-gu', 'Cheese + chicken stir-fry specialty', 19000, '12:00-23:00', NULL),
    ('Bukchon Noodle House', 23, '88 Bukchon-ro, Jongno-gu', 'Homemade noodles and dumplings', 11000, '11:00-20:00', 'Tuesday'),
    ('Namsan Vegan Kitchen', 24, '2 Myeongdong 7-gil, Jung-gu', 'Plant-based/vegan creative plates', 21000, '10:00-22:00', NULL),
    ('Songpa Seoul Pho', 18, '210 Olympic-ro, Songpa-gu', 'Vietnamese pho and rice dishes', 14000, '11:00-22:00', 'Thursday'),
    ('Yeouido Donkatsu', 20, '41 Yeouido-dong, Yeongdeungpo-gu', 'Extra thick pork cutlet, Japanese style', 16000, '11:30-22:00', NULL),
    ('Seocho Gourmet Street', 15, '5 Seocho-daero, Seocho-gu', 'Gourmet alley with various street food', 7500, '09:00-20:00', NULL),
    ('Gangdong Hongli Hotpot', 2, '301 Gangdong-ro, Gangdong-gu', 'Chinese spicy hotpot with buffet', 31000, '17:00-24:00', NULL),
    ('Yongsan Indian Curry', 21, '12 Itaewon-ro 36-gil, Yongsan-gu', 'North & South Indian curry house', 19500, '11:00-22:30', NULL),
    ('Seongbuk Italian Table', 17, '8 Dongsomun-ro, Seongbuk-gu', 'Italian bistro with extensive wine', 32000, '12:30-23:30', 'Friday'),
    ('Gangseo Green Bistro', 4, '2 Hwagok-ro, Gangseo-gu', 'Casual vegetarian lunch, delicious salad', 15400, '11:30-20:00', NULL),
    ('Nowon Jjajangmyeon', 9, '55 Nowon-ro, Nowon-gu', 'Classic Korean-Chinese noodles', 8500, '10:00-22:00', 'Monday'),
    ('Guro Sushi Express', 7, '310 Guro-dong, Guro-gu', 'Quick affordable sushi rolls', 9000, '10:30-21:30', NULL),
    ('Dongjak Soondae House', 12, '17 Noryangjin-ro, Dongjak-gu', 'Best blood sausage and soup', 9000, '09:00-22:00', NULL),
    ('Geumcheon Udon', 8, '22 Doksan-ro, Geumcheon-gu', 'Japanese udon noodles & tempura', 12000, '11:00-21:00', 'Wednesday'),
    ('Dobong Ramen Garage', 10, '88 Dobong-ro, Dobong-gu', 'Late-night ramen and snacks', 16000, '18:00-03:00', NULL),
    ('Mapo Brunch Club', 13, '61 Hongdae-ro, Mapo-gu', 'All-day brunch, pancakes and coffee', 15500, '08:00-18:00', NULL),
    ('Gwanak Chicken Nuggets', 5, '367 Sillim-ro, Gwanak-gu', 'Crispy homemade chicken nuggets', 13500, '12:00-22:00', NULL),
    ('Yangcheon Calzone', 19, '17 Mokdong-ro, Yangcheon-gu', 'Italian calzone and pizza', 21500, '11:00-23:00', NULL),
    ('Seocho Steakhouse', 15, '188 Seocho-daero, Seocho-gu', 'Dry-aged steak and wine', 89000, '17:30-23:30', 'Sunday'),
    ('Songpa Laksa', 18, '50 Songpa-daero, Songpa-gu', 'Singaporean laksa and seafood', 15500, '13:00-21:00', 'Monday'),
    ('Seongdong Vegan Burger', 16, '30 Wangsimni-ro, Seongdong-gu', 'Plant-based burgers & fries', 14000, '10:00-20:00', NULL);

-- 7. Insert restaurant_cuisines (N:M mapping, ~300 rows)
INSERT INTO restaurant_cuisines (restaurant_id, cuisine_id) VALUES
    (1, 1), (2, 1), (3, 11), (4, 1), (5, 1), (6, 1), (7, 1), (8, 1), (9, 1), (10, 1),
    (11, 1), (12, 11), (13, 4), (14, 11), (15, 11), (16, 5), (17, 1), (18, 20), (19, 2), (20, 17),
    (21, 11), (22, 1), (23, 1), (24, 1), (25, 2), (26, 10), (27, 14), (28, 20), (29, 15), (30, 15),
    (31, 18), (32, 1), (33, 11), (34, 6), (35, 14), (36, 12), (37, 1), (38, 1), (39, 1), (40, 12),
    (41, 1), (42, 1), (43, 1), (44, 11), (45, 11), (46, 4), (47, 11), (48, 5), (49, 15), (50, 15),
    (51, 15), (52, 15), (53, 18), (54, 7), (55, 11), (56, 11), (57, 1), (58, 1), (59, 15), (60, 15),
    (61, 15), (62, 15), (63, 20), (64, 14), (65, 14), (66, 1), (67, 5), (68, 1), (69, 1), (70, 1),
    (71, 1), (72, 1), (73, 15), (74, 15), (75, 19), (76, 1), (77, 1), (78, 8), (79, 10), (80, 4),
    (81, 15), (82, 1), (83, 20), (84, 1), (85, 1), (86, 1), (87, 2), (88, 11), (89, 11), (90, 11),
    (91, 1), (92, 2), (93, 1), (94, 1), (95, 4), (96, 1), (97, 1), (98, 2), (99, 11), (100, 1),
    (101, 1), (102, 13), (103, 12), (104, 1), (105, 1), (106, 19), (107, 15), (108, 2),
    (109, 10), (110, 11), (111, 2), (112, 18), (113, 8), (114, 12), (115, 15), (116, 3),
    (117, 9), (118, 5), (119, 18), (120, 3), (121, 2), (122, 3), (123, 7), (124, 2),
    (125, 15), (126, 12), (127, 14), (128, 4), (129, 18), (130, 19);

-- Add secondary cuisines for some restaurants
INSERT INTO restaurant_cuisines (restaurant_id, cuisine_id) VALUES
    (1, 19), (2, 19), (8, 6), (11, 6), (16, 19), (17, 19), (19, 17), (20, 1),
    (22, 19), (26, 1), (31, 1), (32, 19), (33, 1), (34, 5), (46, 1), (48, 6),
    (51, 16), (53, 1), (54, 1), (66, 19), (67, 4), (75, 1), (79, 1), (87, 17),
    (91, 6), (92, 17), (93, 2), (94, 6), (95, 6), (98, 1);

-- 8. Insert reviews (1000 rows, varied combinations)
INSERT INTO reviews (
    user_id, restaurant_id, occasion_id, time_slot_id,
    rating_score, spend_amount, comment, visit_time
)
SELECT
    -- user_id: 1~100 분포
    (n % 100) + 1                                   AS user_id,
    -- restaurant_id: 1~130 분포
    ((n * 7) % 130) + 1                             AS restaurant_id,
    -- occasion_id: 1~10
    ((n * 47 + 61) % 10) + 1                        AS occasion_id,
    -- time_slot_id: 1~5
    ((n * 33 + 7) % 5) + 1                          AS time_slot_id,
    -- rating_score: 1~5, 식당·유저 조합마다 좀 섞이도록 패턴화
    CASE 
        WHEN n % 11 IN (0,1,2,3) THEN 5
        WHEN n % 11 IN (4,5,6)   THEN 4
        WHEN n % 11 IN (7,8)     THEN 3
        WHEN n % 11 = 9          THEN 2
        ELSE 1
    END                                             AS rating_score,
    -- spend_amount: 15,000 ~ 200,000 정도에서 다양하게
    CASE 
        WHEN ((n * 7) % 130) + 1 <= 26  THEN 90000  + (n % 6)  * 10000  -- 고급 식당
        WHEN ((n * 7) % 130) + 1 <= 52  THEN 60000  + (n % 8)  * 5000   -- 중상 가격
        WHEN ((n * 7) % 130) + 1 <= 91  THEN 30000  + (n % 10) * 3000   -- 중간 가격
        ELSE                                  7000  + (n % 12) * 2000   -- 저가/캐주얼
    END                                             AS spend_amount,
    -- comment: 20종 정도 패턴 돌려쓰기
    CASE (n % 20)
        WHEN 0  THEN 'Excellent food and atmosphere! Will definitely come back.'
        WHEN 1  THEN 'Great place for special occasions. Service was impeccable.'
        WHEN 2  THEN 'Food was delicious and portions were generous.'
        WHEN 3  THEN 'Good value for money. Recommended for casual dining.'
        WHEN 4  THEN 'Perfect spot for a date. Romantic ambiance and cozy interior.'
        WHEN 5  THEN 'Family-friendly restaurant with a wide variety of dishes.'
        WHEN 6  THEN 'Quick service and tasty food. Ideal for lunch breaks.'
        WHEN 7  THEN 'Authentic flavors with traditional cooking style.'
        WHEN 8  THEN 'Modern twist on classic dishes. Very creative menu.'
        WHEN 9  THEN 'Cozy atmosphere, great for catching up with friends.'
        WHEN 10 THEN 'High quality ingredients. Worth the premium price.'
        WHEN 11 THEN 'Decent food but nothing extraordinary. Could be better.'
        WHEN 12 THEN 'Great for business meetings. Calm and professional setting.'
        WHEN 13 THEN 'Solo dining friendly. Comfortable seating and service.'
        WHEN 14 THEN 'Enjoyable experience overall. Would visit again.'
        WHEN 15 THEN 'Amazing desserts and coffee. Perfect afternoon spot.'
        WHEN 16 THEN 'Staff were very kind and attentive throughout the meal.'
        WHEN 17 THEN 'Lively atmosphere, good for group gatherings and parties.'
        WHEN 18 THEN 'Portions were slightly small for the price, but tasty.'
        ELSE        'Reservation is recommended. The place gets crowded quickly.'
    END                                             AS comment,
    -- visit_time: 최근 365일 안의 랜덤-ish 날짜/시간
    DATE_SUB(
        DATE_ADD(
            CURDATE(),
            INTERVAL (n % 24) HOUR
        ),
        INTERVAL (n % 365) DAY
    )                                               AS visit_time
FROM (
    -- n: 0 ~ 999 생성 (총 1000개)
    SELECT 
        (a.n + b.n * 10 + c.n * 100) AS n
    FROM 
        (SELECT 0 AS n UNION SELECT 1 UNION SELECT 2 UNION SELECT 3 UNION SELECT 4 
         UNION SELECT 5 UNION SELECT 6 UNION SELECT 7 UNION SELECT 8 UNION SELECT 9) a,
        (SELECT 0 AS n UNION SELECT 1 UNION SELECT 2 UNION SELECT 3 UNION SELECT 4 
         UNION SELECT 5 UNION SELECT 6 UNION SELECT 7 UNION SELECT 8 UNION SELECT 9) b,
        (SELECT 0 AS n UNION SELECT 1 UNION SELECT 2 UNION SELECT 3 UNION SELECT 4 
         UNION SELECT 5 UNION SELECT 6 UNION SELECT 7 UNION SELECT 8 UNION SELECT 9) c
    WHERE (a.n + b.n * 10 + c.n * 100) < 1000
) numbers;

-- Update restaurant statistics based on reviews
UPDATE restaurants r
SET 
    avg_rating = (
        SELECT COALESCE(CAST(AVG(rating_score) AS DECIMAL(3,2)), 0.0)
        FROM reviews 
        WHERE restaurant_id = r.restaurant_id
    ),
    review_count = (
        SELECT COUNT(*) 
        FROM reviews 
        WHERE restaurant_id = r.restaurant_id
    );

-- Re-enable foreign key checks
SET FOREIGN_KEY_CHECKS = 1;

-- Display summary
SELECT 'Data insertion completed!' as message;
SELECT 'Districts:' as category, COUNT(*) as count FROM districts
UNION ALL
SELECT 'Cuisines:', COUNT(*) FROM cuisines
UNION ALL
SELECT 'Occasions:', COUNT(*) FROM occasions
UNION ALL
SELECT 'Time Slots:', COUNT(*) FROM time_slots
UNION ALL
SELECT 'Users:', COUNT(*) FROM users
UNION ALL
SELECT 'Restaurants:', COUNT(*) FROM restaurants
UNION ALL
SELECT 'Restaurant-Cuisines:', COUNT(*) FROM restaurant_cuisines
UNION ALL
SELECT 'Reviews:', COUNT(*) FROM reviews
UNION ALL
SELECT 'TOTAL ROWS:', 
    (SELECT COUNT(*) FROM districts) + 
    (SELECT COUNT(*) FROM cuisines) +
    (SELECT COUNT(*) FROM occasions) +
    (SELECT COUNT(*) FROM time_slots) +
    (SELECT COUNT(*) FROM users) +
    (SELECT COUNT(*) FROM restaurants) +
    (SELECT COUNT(*) FROM restaurant_cuisines) +
    (SELECT COUNT(*) FROM reviews);
