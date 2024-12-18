/*
 Navicat Premium Data Transfer

 Source Server         : db_local
 Source Server Type    : MySQL
 Source Server Version : 100425 (10.4.25-MariaDB)
 Source Host           : localhost:3306
 Source Schema         : db_slim3

 Target Server Type    : MySQL
 Target Server Version : 100425 (10.4.25-MariaDB)
 File Encoding         : 65001

 Date: 27/10/2022 09:01:14
*/

SET NAMES utf8mb4;
SET FOREIGN_KEY_CHECKS = 0;

-- ----------------------------
-- Table structure for tbl_guest_entry
-- ----------------------------
DROP TABLE IF EXISTS `tbl_guest_entry`;
CREATE TABLE `tbl_guest_entry`  (
  `id` int NOT NULL AUTO_INCREMENT,
  `full_name` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL DEFAULT NULL,
  `email` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL DEFAULT NULL,
  `comment` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL DEFAULT NULL,
  `id_prodi` int NULL DEFAULT 1,
  `created_at` datetime NULL DEFAULT NULL,
  `updated_at` datetime NULL DEFAULT NULL,
  PRIMARY KEY (`id`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 16 CHARACTER SET = utf8mb4 COLLATE = utf8mb4_general_ci ROW_FORMAT = DYNAMIC;

-- ----------------------------
-- Records of tbl_guest_entry
-- ----------------------------
INSERT INTO `tbl_guest_entry` VALUES (1, 'Malik Ibrahim', 'malikibrahim@gmail.com', 'name malik ibrahim', 1, '2022-08-21 01:40:40', NULL);
INSERT INTO `tbl_guest_entry` VALUES (2, 'Marquest', 'marquest@gmail.com', 'my name is marquest', 1, '2022-08-21 01:41:20', NULL);
INSERT INTO `tbl_guest_entry` VALUES (3, 'Boy Manner', 'boymanner@gmail.com', 'hi boy manner bro', 2, '2022-08-21 01:41:55', NULL);
INSERT INTO `tbl_guest_entry` VALUES (4, 'Zulkarnain', 'zulkarnain@gmail.com', 'me. zurkarnain', 2, '2022-08-21 01:42:35', NULL);
INSERT INTO `tbl_guest_entry` VALUES (5, 'Roy bob', 'roybob@gmail.com', 'hoooy, Roy Bob', 3, '2022-08-21 01:43:16', NULL);
INSERT INTO `tbl_guest_entry` VALUES (6, 'Bayu P', 'bayup@gmail.com', 'Bay u P', 3, '2022-08-23 01:05:34', '2022-08-23 01:05:34');
INSERT INTO `tbl_guest_entry` VALUES (7, 'Bayu P', 'bayup@gmail.com', 'Bay u P', 4, '2022-08-23 01:05:38', '2022-08-23 01:05:38');
INSERT INTO `tbl_guest_entry` VALUES (8, 'Bayu P', 'bayup@gmail.com', 'Bay u P', 4, '2022-08-23 01:05:48', '2022-08-23 01:05:48');
INSERT INTO `tbl_guest_entry` VALUES (9, 'Bayu P', 'bayup@gmail.com', 'Bay u P', 5, '2022-08-23 01:06:31', '2022-08-23 01:06:31');
INSERT INTO `tbl_guest_entry` VALUES (10, 'Bayu P', 'bayup@gmail.com', 'Bay u P', 5, '2022-08-23 01:09:47', NULL);
INSERT INTO `tbl_guest_entry` VALUES (11, 'Bayu P', 'bayup@gmail.com', 'Bay u P', 6, '2022-08-23 01:09:54', NULL);
INSERT INTO `tbl_guest_entry` VALUES (12, 'Bayu P', 'bayup@gmail.com', 'Bay u P45', 6, '2022-10-27 07:20:51', '2022-08-23 01:45:21');
INSERT INTO `tbl_guest_entry` VALUES (14, 'Bayu P', 'bayup@gmail.com', 'Bay u P', 6, '2022-08-23 01:22:15', NULL);

-- ----------------------------
-- Table structure for tbl_prodi
-- ----------------------------
DROP TABLE IF EXISTS `tbl_prodi`;
CREATE TABLE `tbl_prodi`  (
  `id` int NOT NULL AUTO_INCREMENT,
  `name` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL DEFAULT NULL,
  `slug` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL DEFAULT NULL,
  `created_at` datetime NULL DEFAULT NULL,
  `updated_at` datetime NULL DEFAULT NULL,
  PRIMARY KEY (`id`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 7 CHARACTER SET = utf8mb4 COLLATE = utf8mb4_general_ci ROW_FORMAT = DYNAMIC;

-- ----------------------------
-- Records of tbl_prodi
-- ----------------------------
INSERT INTO `tbl_prodi` VALUES (1, 'Teknik Informatika', 'ti', '2022-08-24 00:30:54', NULL);
INSERT INTO `tbl_prodi` VALUES (2, 'Mesin', 'mesin', '2022-08-24 00:31:15', NULL);
INSERT INTO `tbl_prodi` VALUES (3, 'Biologi', 'biologi', '2022-08-24 00:31:28', NULL);
INSERT INTO `tbl_prodi` VALUES (4, 'Sipil', 'sipil', '2022-08-24 00:31:49', NULL);
INSERT INTO `tbl_prodi` VALUES (5, 'Perminyakan', 'perminyakan', '2022-08-24 00:32:10', NULL);
INSERT INTO `tbl_prodi` VALUES (6, 'Industri', 'industri', '2022-08-24 00:32:57', NULL);

-- ----------------------------
-- Table structure for tbl_user
-- ----------------------------
DROP TABLE IF EXISTS `tbl_user`;
CREATE TABLE `tbl_user`  (
  `id` int NOT NULL AUTO_INCREMENT,
  `name` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL DEFAULT NULL,
  `email` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL DEFAULT NULL,
  `password` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL DEFAULT NULL,
  `api_key` text CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL,
  `created_at` datetime NULL DEFAULT NULL,
  `updated_at` datetime NULL DEFAULT NULL,
  PRIMARY KEY (`id`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 5 CHARACTER SET = utf8mb4 COLLATE = utf8mb4_general_ci ROW_FORMAT = DYNAMIC;

-- ----------------------------
-- Records of tbl_user
-- ----------------------------
INSERT INTO `tbl_user` VALUES (3, 'user2', 'user2@gmail.com', '$2y$10$MLLPJBZZqKEX8GWXVo9IzugX1J.hzkfhzMsGgqUkxg4FZA5/xhYaq', 'qwert', '2022-08-20 20:22:20', '2022-08-20 20:22:20');
INSERT INTO `tbl_user` VALUES (4, 'user100', 'user100@gmail.com', '$2y$10$QAItqP6hnXDYVfXF6OkMReJOSwv7kN/hhQ55vKpbPYchDI7ckLOyC', 'fghjk', '2022-08-23 19:11:00', '2022-08-23 19:11:00');

SET FOREIGN_KEY_CHECKS = 1;
