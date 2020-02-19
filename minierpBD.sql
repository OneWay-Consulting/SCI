-- phpMyAdmin SQL Dump
-- version 4.1.14
-- http://www.phpmyadmin.net
--
-- Servidor: 127.0.0.1
-- Tiempo de generación: 18-02-2020 a las 22:34:27
-- Versión del servidor: 5.6.17
-- Versión de PHP: 5.5.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;

--
-- Base de datos: `onewayin_minerp`
--

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `tcbalance`
--

CREATE TABLE IF NOT EXISTS `tcbalance` (
  `pnid` int(11) NOT NULL AUTO_INCREMENT,
  `dstype` varchar(20) COLLATE utf8_bin NOT NULL,
  `fniddocument` int(11) NOT NULL,
  `dddate` datetime NOT NULL,
  `dnamount` decimal(10,2) NOT NULL,
  `dscomments` varchar(200) COLLATE utf8_bin NOT NULL,
  `fnidpartner` int(11) DEFAULT NULL,
  `fniddelivery` int(11) DEFAULT NULL,
  PRIMARY KEY (`pnid`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_bin COMMENT='tabla de pagos' AUTO_INCREMENT=2820 ;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `tcbranch`
--

CREATE TABLE IF NOT EXISTS `tcbranch` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `dsname` varchar(20) COLLATE utf8_bin NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_bin COMMENT='tabla de marcas' AUTO_INCREMENT=15 ;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `tccompany`
--

CREATE TABLE IF NOT EXISTS `tccompany` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `dsname` varchar(20) COLLATE utf8_bin NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_bin COMMENT='tabla de empresa' AUTO_INCREMENT=6 ;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `tcitem`
--

CREATE TABLE IF NOT EXISTS `tcitem` (
  `pnid` int(11) NOT NULL AUTO_INCREMENT,
  `dscode` varchar(50) COLLATE utf8_bin NOT NULL,
  `dsname` varchar(100) COLLATE utf8_bin NOT NULL,
  `dsactive` tinyint(1) NOT NULL DEFAULT '1',
  `dsupc` varchar(50) COLLATE utf8_bin DEFAULT NULL,
  `dscomments` varchar(250) COLLATE utf8_bin DEFAULT NULL,
  `dsserial` tinyint(1) NOT NULL DEFAULT '0',
  `fnidbranch` int(11) NOT NULL,
  `fnidcompany` int(11) NOT NULL,
  `dspadre` varchar(50) COLLATE utf8_bin DEFAULT NULL,
  `dsstatus` varchar(50) COLLATE utf8_bin DEFAULT 'Nuevo',
  `dsupc2` varchar(100) COLLATE utf8_bin NOT NULL,
  PRIMARY KEY (`pnid`),
  UNIQUE KEY `dscode` (`dscode`),
  UNIQUE KEY `dsname` (`dsname`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_bin COMMENT='tabla de articulos' AUTO_INCREMENT=1074 ;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `tcmodule`
--

CREATE TABLE IF NOT EXISTS `tcmodule` (
  `pnid` int(11) NOT NULL AUTO_INCREMENT,
  `dspage` varchar(100) COLLATE utf8_bin NOT NULL,
  `dscomment` varchar(200) COLLATE utf8_bin NOT NULL,
  PRIMARY KEY (`pnid`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_bin COMMENT='tabla para modulos accecibles' AUTO_INCREMENT=23 ;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `tcmoduleperm`
--

CREATE TABLE IF NOT EXISTS `tcmoduleperm` (
  `pnid` int(11) NOT NULL AUTO_INCREMENT,
  `fnidrole` int(11) NOT NULL,
  `fnidmodule` int(11) NOT NULL,
  `dncreated` int(11) NOT NULL DEFAULT '0',
  `dnquery` int(11) NOT NULL DEFAULT '0',
  PRIMARY KEY (`pnid`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_bin COMMENT='tabla de configuración de permisos por modulo' AUTO_INCREMENT=154 ;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `tcpartner`
--

CREATE TABLE IF NOT EXISTS `tcpartner` (
  `pnid` int(11) NOT NULL AUTO_INCREMENT,
  `dscode` varchar(50) COLLATE utf8_bin NOT NULL,
  `dsname` varchar(100) COLLATE utf8_bin NOT NULL,
  `dsrfc` varchar(15) COLLATE utf8_bin DEFAULT NULL,
  `dsaddress` varchar(200) COLLATE utf8_bin DEFAULT NULL,
  `dstype` varchar(2) COLLATE utf8_bin DEFAULT NULL,
  `dsemail` varchar(50) COLLATE utf8_bin DEFAULT NULL,
  `dsphone` varchar(50) COLLATE utf8_bin DEFAULT NULL,
  `dscredit` varchar(2) COLLATE utf8_bin DEFAULT 'N',
  `dncreditday` int(11) DEFAULT '0',
  `dddiscountperc` decimal(10,2) NOT NULL DEFAULT '0.00' COMMENT 'porcentaje de descuento',
  `dsactive` int(1) NOT NULL DEFAULT '1',
  `dbvalidateguia` int(1) DEFAULT '1',
  PRIMARY KEY (`pnid`),
  UNIQUE KEY `dscode` (`dscode`),
  UNIQUE KEY `dsname` (`dsname`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_bin COMMENT='tabla de almacenamiento de socios de negocios' AUTO_INCREMENT=156 ;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `tcrole`
--

CREATE TABLE IF NOT EXISTS `tcrole` (
  `idtcrole` int(11) NOT NULL AUTO_INCREMENT,
  `dsname` varchar(45) NOT NULL,
  `dsdescription` varchar(200) NOT NULL,
  PRIMARY KEY (`idtcrole`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COMMENT='table with the catalog from role involved whit the system' AUTO_INCREMENT=8 ;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `tcstatus`
--

CREATE TABLE IF NOT EXISTS `tcstatus` (
  `pnid` int(11) NOT NULL AUTO_INCREMENT,
  `dsname` varchar(50) COLLATE utf8_bin NOT NULL,
  `dsdescription` varchar(100) COLLATE utf8_bin NOT NULL,
  PRIMARY KEY (`pnid`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_bin AUTO_INCREMENT=7 ;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `tcuser`
--

CREATE TABLE IF NOT EXISTS `tcuser` (
  `idtcuser` int(11) NOT NULL AUTO_INCREMENT,
  `dsuser` varchar(45) NOT NULL,
  `dspassword` varchar(45) NOT NULL,
  `fnidrole` int(11) NOT NULL,
  `dnactivo` int(11) NOT NULL DEFAULT '0',
  `dsemail` varchar(45) NOT NULL,
  `dsnombrecom` varchar(100) NOT NULL,
  `fncardcode` varchar(100) NOT NULL,
  `dscardname` varchar(100) NOT NULL,
  `fnproject` varchar(20) NOT NULL,
  `dsprojectname` varchar(100) NOT NULL,
  `GroupCode` varchar(10) DEFAULT NULL,
  `whscode` varchar(20) DEFAULT NULL,
  `series` varchar(10) DEFAULT NULL,
  PRIMARY KEY (`idtcuser`),
  KEY `fnidrole` (`fnidrole`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COMMENT='this table store the user catalog	' AUTO_INCREMENT=58 ;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `tcwarehouse`
--

CREATE TABLE IF NOT EXISTS `tcwarehouse` (
  `pnid` int(11) NOT NULL AUTO_INCREMENT,
  `dscode` varchar(50) COLLATE utf8_bin NOT NULL,
  `dsname` varchar(200) COLLATE utf8_bin NOT NULL,
  `dscomments` varchar(250) COLLATE utf8_bin DEFAULT NULL,
  `dbactive` int(1) NOT NULL DEFAULT '1',
  PRIMARY KEY (`pnid`),
  UNIQUE KEY `dscode` (`dscode`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_bin COMMENT='tabla de almacenes' AUTO_INCREMENT=6 ;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `thdelivery`
--

CREATE TABLE IF NOT EXISTS `thdelivery` (
  `pnid` int(11) NOT NULL AUTO_INCREMENT,
  `dddate` date NOT NULL,
  `dscomment` varchar(200) COLLATE utf8_bin NOT NULL,
  PRIMARY KEY (`pnid`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_bin COMMENT='tabla de relación salidas de inventario' AUTO_INCREMENT=3878 ;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `thentry`
--

CREATE TABLE IF NOT EXISTS `thentry` (
  `pnid` int(11) NOT NULL AUTO_INCREMENT,
  `dddate` datetime NOT NULL,
  `dscomment` varchar(50) COLLATE utf8_bin NOT NULL,
  PRIMARY KEY (`pnid`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_bin COMMENT='tabla de folios de entrada' AUTO_INCREMENT=619 ;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `thpricelist`
--

CREATE TABLE IF NOT EXISTS `thpricelist` (
  `idlist` int(11) NOT NULL AUTO_INCREMENT,
  `fnidclient` int(11) DEFAULT '0',
  `dsname` varchar(100) COLLATE utf8_bin NOT NULL,
  `dnactive` int(11) NOT NULL DEFAULT '0',
  `dbbase` int(11) DEFAULT '0',
  PRIMARY KEY (`idlist`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_bin COMMENT='cabecera lista de precios' AUTO_INCREMENT=2 ;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `thpricelistitem`
--

CREATE TABLE IF NOT EXISTS `thpricelistitem` (
  `pnidtr` int(11) NOT NULL AUTO_INCREMENT,
  `fnidheader` int(11) NOT NULL,
  `fniditem` int(11) NOT NULL,
  `dsitemcode` varchar(50) COLLATE utf8_bin NOT NULL,
  `ddprice` decimal(10,2) NOT NULL,
  PRIMARY KEY (`pnidtr`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_bin COMMENT='tabla de relación precios' AUTO_INCREMENT=2038 ;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `thpurchase`
--

CREATE TABLE IF NOT EXISTS `thpurchase` (
  `pnid` int(11) NOT NULL AUTO_INCREMENT,
  `fnidpartner` int(11) NOT NULL,
  `dscode` varchar(50) COLLATE utf8_bin NOT NULL,
  `dsname` varchar(200) COLLATE utf8_bin NOT NULL,
  `fnidcreator` int(11) NOT NULL,
  `dsreference` varchar(50) COLLATE utf8_bin NOT NULL,
  `dsstatus` varchar(20) COLLATE utf8_bin NOT NULL,
  `ddcreated` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `ddupdate` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
  `dddocdate` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
  PRIMARY KEY (`pnid`),
  KEY `fk_partner` (`fnidpartner`),
  KEY `fk_creator` (`fnidcreator`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_bin COMMENT='cabecera de compras' AUTO_INCREMENT=263 ;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `thpurchaseline`
--

CREATE TABLE IF NOT EXISTS `thpurchaseline` (
  `pnid` int(11) NOT NULL AUTO_INCREMENT,
  `thidheader` int(11) NOT NULL,
  `fniditem` int(11) NOT NULL,
  `dscode` varchar(50) COLLATE utf8_bin NOT NULL,
  `dsname` varchar(100) COLLATE utf8_bin NOT NULL,
  `fnidware` int(11) NOT NULL,
  `dnquantity` decimal(10,2) NOT NULL,
  `dniva` decimal(10,2) NOT NULL,
  `dnprice` decimal(10,2) NOT NULL,
  `dnopenqty` decimal(10,2) NOT NULL DEFAULT '0.00',
  `ddarrive` date DEFAULT NULL,
  `dsref` varchar(100) COLLATE utf8_bin DEFAULT NULL,
  PRIMARY KEY (`pnid`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_bin COMMENT='linea de compras' AUTO_INCREMENT=3509 ;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `thpurchaseserial`
--

CREATE TABLE IF NOT EXISTS `thpurchaseserial` (
  `pnid` int(11) NOT NULL AUTO_INCREMENT,
  `fnidline` int(11) NOT NULL,
  `fnidserial` varchar(50) COLLATE utf8_bin NOT NULL,
  `fnidquantity` decimal(10,0) NOT NULL,
  `fnidheader` int(11) NOT NULL,
  `fnidheaderentry` int(11) DEFAULT NULL,
  `fnidware` int(11) DEFAULT '1',
  PRIMARY KEY (`pnid`),
  UNIQUE KEY `fnidserial` (`fnidserial`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_bin AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `thquote`
--

CREATE TABLE IF NOT EXISTS `thquote` (
  `pnid` int(11) NOT NULL AUTO_INCREMENT,
  `fnidpartner` int(11) NOT NULL,
  `dscode` varchar(50) COLLATE utf8_bin NOT NULL,
  `dsname` varchar(200) COLLATE utf8_bin NOT NULL,
  `fnidcreator` int(11) NOT NULL,
  `dsreference` varchar(50) COLLATE utf8_bin NOT NULL,
  `dsstatus` varchar(20) COLLATE utf8_bin NOT NULL,
  `ddcreated` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `ddupdate` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
  `dddocdate` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
  `dscomments` varchar(250) COLLATE utf8_bin DEFAULT NULL,
  PRIMARY KEY (`pnid`),
  KEY `fk_partner` (`fnidpartner`),
  KEY `fk_creator` (`fnidcreator`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_bin COMMENT='cabecera de cotizacion' AUTO_INCREMENT=4328 ;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `thquoteline`
--

CREATE TABLE IF NOT EXISTS `thquoteline` (
  `pnid` int(11) NOT NULL AUTO_INCREMENT,
  `thidheader` int(11) NOT NULL,
  `fniditem` int(11) NOT NULL,
  `dscode` varchar(50) COLLATE utf8_bin NOT NULL,
  `dsname` varchar(100) COLLATE utf8_bin NOT NULL,
  `fnidware` int(11) NOT NULL,
  `dnquantity` decimal(10,0) NOT NULL,
  `dniva` decimal(10,0) NOT NULL,
  `dnprice` decimal(10,2) NOT NULL,
  `dsguia` varchar(50) COLLATE utf8_bin DEFAULT NULL,
  `dscanal` varchar(50) COLLATE utf8_bin DEFAULT NULL,
  `dsstatus` varchar(20) COLLATE utf8_bin DEFAULT NULL,
  `dspaqueteria` varchar(100) COLLATE utf8_bin DEFAULT NULL,
  `dscomentariol` varchar(250) COLLATE utf8_bin DEFAULT NULL,
  `dsstatusaut` varchar(10) COLLATE utf8_bin DEFAULT NULL,
  `dsuseraut` varchar(10) COLLATE utf8_bin DEFAULT NULL,
  `dnlinedest` int(11) DEFAULT NULL,
  `dnopenqty` decimal(10,0) DEFAULT '0',
  `dsrefline` varchar(100) COLLATE utf8_bin DEFAULT NULL,
  PRIMARY KEY (`pnid`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_bin COMMENT='linea de cotizacion' AUTO_INCREMENT=64103 ;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `threturn`
--

CREATE TABLE IF NOT EXISTS `threturn` (
  `pnid` int(11) NOT NULL AUTO_INCREMENT,
  `fnidpartner` int(11) NOT NULL,
  `dspartnercode` varchar(20) COLLATE utf8_bin NOT NULL,
  `dsreference` varchar(100) COLLATE utf8_bin DEFAULT NULL,
  `dspartnername` varchar(100) COLLATE utf8_bin NOT NULL,
  `dddate` date NOT NULL,
  `dscomments` int(11) NOT NULL,
  PRIMARY KEY (`pnid`),
  KEY `pnid` (`pnid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_bin COMMENT='cabecera de devoluciones' AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `threturnline`
--

CREATE TABLE IF NOT EXISTS `threturnline` (
  `pnid` int(11) NOT NULL AUTO_INCREMENT,
  `fnidheader` int(11) NOT NULL,
  `fniditem` int(11) NOT NULL,
  `dscode` varchar(50) COLLATE utf8_bin NOT NULL,
  `dnquantity` decimal(10,2) NOT NULL,
  `dnprice` decimal(10,2) DEFAULT NULL,
  `dsrefline` varchar(40) COLLATE utf8_bin NOT NULL,
  `fnidware` int(11) NOT NULL,
  `fnidlineori` int(11) DEFAULT NULL,
  PRIMARY KEY (`pnid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_bin AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `threturnserial`
--

CREATE TABLE IF NOT EXISTS `threturnserial` (
  `pnid` int(11) NOT NULL AUTO_INCREMENT,
  `fnidline` int(11) NOT NULL,
  `fnidserial` varchar(30) COLLATE utf8_bin NOT NULL,
  `fnidquantity` int(11) NOT NULL,
  `fnidheader` int(11) NOT NULL,
  PRIMARY KEY (`pnid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_bin AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `thsales`
--

CREATE TABLE IF NOT EXISTS `thsales` (
  `pnid` int(11) NOT NULL AUTO_INCREMENT,
  `fnidpartner` int(11) NOT NULL,
  `dscode` varchar(50) COLLATE utf8_bin NOT NULL,
  `dsname` varchar(200) COLLATE utf8_bin NOT NULL,
  `fnidcreator` int(11) NOT NULL,
  `dsreference` varchar(50) COLLATE utf8_bin NOT NULL,
  `dsstatus` varchar(20) COLLATE utf8_bin NOT NULL,
  `ddcreated` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `ddupdate` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
  `dddocdate` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
  `dscomments` varchar(250) COLLATE utf8_bin DEFAULT NULL,
  `dsstatuscredit` varchar(20) COLLATE utf8_bin DEFAULT '1' COMMENT 'estatus para cobranza',
  PRIMARY KEY (`pnid`),
  KEY `fk_partner` (`fnidpartner`),
  KEY `fk_creator` (`fnidcreator`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_bin COMMENT='cabecera de ventas' AUTO_INCREMENT=4309 ;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `thsalesline`
--

CREATE TABLE IF NOT EXISTS `thsalesline` (
  `pnid` int(11) NOT NULL AUTO_INCREMENT,
  `thidheader` int(11) NOT NULL,
  `fniditem` int(11) NOT NULL,
  `dscode` varchar(50) COLLATE utf8_bin NOT NULL,
  `dsname` varchar(100) COLLATE utf8_bin NOT NULL,
  `fnidware` int(11) NOT NULL,
  `dnquantity` decimal(10,0) NOT NULL,
  `dniva` decimal(10,0) NOT NULL,
  `dnprice` decimal(10,2) NOT NULL,
  `dsguia` varchar(50) COLLATE utf8_bin DEFAULT NULL,
  `dscanal` varchar(50) COLLATE utf8_bin DEFAULT NULL,
  `dsstatus` varchar(20) COLLATE utf8_bin DEFAULT NULL,
  `dspaqueteria` varchar(100) COLLATE utf8_bin DEFAULT NULL,
  `dscomentariol` varchar(250) COLLATE utf8_bin DEFAULT NULL,
  `dnlineori` int(11) NOT NULL,
  `dsstatusguia` varchar(1) COLLATE utf8_bin DEFAULT 'C' COMMENT 'C- Creado, S - Surtido, E - Entregado',
  `dsguiac` varchar(200) COLLATE utf8_bin DEFAULT NULL,
  `dsrefline` varchar(100) COLLATE utf8_bin DEFAULT NULL,
  `dnopenqty` decimal(10,2) DEFAULT '0.00',
  PRIMARY KEY (`pnid`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_bin COMMENT='linea de compras' AUTO_INCREMENT=61879 ;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `thsalesserial`
--

CREATE TABLE IF NOT EXISTS `thsalesserial` (
  `pnid` int(11) NOT NULL AUTO_INCREMENT,
  `fnidline` int(11) NOT NULL,
  `fnidserial` varchar(50) COLLATE utf8_bin NOT NULL,
  `fnidquantity` decimal(10,0) NOT NULL,
  `fnidheader` int(11) NOT NULL,
  `fnidheaderdelivery` int(11) DEFAULT NULL,
  PRIMARY KEY (`pnid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_bin AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `thtransfer`
--

CREATE TABLE IF NOT EXISTS `thtransfer` (
  `pnid` int(11) NOT NULL AUTO_INCREMENT,
  `dsstatus` varchar(20) COLLATE utf8_bin DEFAULT NULL,
  `ddate` datetime NOT NULL,
  `fniduser` int(11) NOT NULL,
  `dscomments` varchar(250) COLLATE utf8_bin NOT NULL,
  `fnidfromware` int(11) NOT NULL,
  `fnidtoware` int(11) NOT NULL,
  `dsreference` varchar(30) COLLATE utf8_bin DEFAULT NULL,
  PRIMARY KEY (`pnid`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_bin COMMENT='cabecera de transferencias' AUTO_INCREMENT=8 ;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `thtransferfol`
--

CREATE TABLE IF NOT EXISTS `thtransferfol` (
  `pnid` int(11) NOT NULL AUTO_INCREMENT,
  `dddate` datetime NOT NULL,
  `fnididtransfer` varchar(20) COLLATE utf8_bin NOT NULL,
  `dscomments` varchar(100) COLLATE utf8_bin NOT NULL,
  PRIMARY KEY (`pnid`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_bin COMMENT='folio de confirmacion transferencia' AUTO_INCREMENT=5 ;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `thtransferline`
--

CREATE TABLE IF NOT EXISTS `thtransferline` (
  `pnid` int(11) NOT NULL AUTO_INCREMENT,
  `fnidheader` int(11) NOT NULL,
  `fniditem` int(11) NOT NULL,
  `dscode` varchar(20) COLLATE utf8_bin NOT NULL,
  `dsname` varchar(100) COLLATE utf8_bin NOT NULL,
  `dnquantity` decimal(10,2) NOT NULL,
  `dnopenqty` decimal(10,2) NOT NULL,
  `fnidfrom` int(11) DEFAULT NULL,
  `fnidto` int(11) DEFAULT NULL,
  KEY `fk_fnidheader` (`fnidheader`),
  KEY `fk_fniditem` (`fniditem`),
  KEY `pnid` (`pnid`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_bin COMMENT='linea de transferencias' AUTO_INCREMENT=6 ;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `thtransferserial`
--

CREATE TABLE IF NOT EXISTS `thtransferserial` (
  `pnid` int(11) NOT NULL AUTO_INCREMENT,
  `fnidline` int(11) NOT NULL,
  `dsserial` varchar(30) COLLATE utf8_bin NOT NULL,
  `fnidheader` int(11) NOT NULL,
  `dnquantity` decimal(10,2) NOT NULL,
  `fnidconfirm` int(11) DEFAULT NULL,
  PRIMARY KEY (`pnid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_bin COMMENT='tabla de transferencias SERIES' AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `thtransformation`
--

CREATE TABLE IF NOT EXISTS `thtransformation` (
  `pnid` int(11) NOT NULL AUTO_INCREMENT,
  `dddate` datetime NOT NULL,
  `fniduser` int(11) NOT NULL,
  `dscomments` varchar(200) COLLATE utf8_bin NOT NULL,
  PRIMARY KEY (`pnid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_bin COMMENT='cabecera para transformacion de SKU' AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `thtransformationline`
--

CREATE TABLE IF NOT EXISTS `thtransformationline` (
  `pnid` int(11) NOT NULL AUTO_INCREMENT,
  `fnidheader` int(11) NOT NULL,
  `fnidocori` int(11) NOT NULL,
  `fnidlineori` int(11) NOT NULL,
  `fniditemori` int(11) NOT NULL,
  `dsserial` varchar(50) COLLATE utf8_bin NOT NULL,
  `fniditemnew` int(11) NOT NULL,
  `fnidocnew` int(11) NOT NULL,
  `fnidlinenew` int(11) NOT NULL,
  `fnidware` int(11) NOT NULL,
  PRIMARY KEY (`pnid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_bin COMMENT='lineas de transformacion SKU' AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `trcost`
--

CREATE TABLE IF NOT EXISTS `trcost` (
  `pnid` int(11) NOT NULL AUTO_INCREMENT,
  `fniditem` int(11) NOT NULL,
  `dscode` decimal(50,0) NOT NULL,
  `ddqtyinv` decimal(10,2) NOT NULL,
  `ddcostinv` decimal(10,2) NOT NULL,
  `ddcostinvant` decimal(10,2) NOT NULL,
  `ddqtyinvant` decimal(10,2) NOT NULL,
  `fniddocori` int(11) NOT NULL,
  `dddate` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `dsdirection` int(11) NOT NULL DEFAULT '1' COMMENT '1 = entrada, -1 = salida',
  PRIMARY KEY (`pnid`),
  KEY `pnid` (`pnid`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_bin COMMENT='tabla de costos' AUTO_INCREMENT=39182 ;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `trinventory`
--

CREATE TABLE IF NOT EXISTS `trinventory` (
  `pnid` int(11) NOT NULL AUTO_INCREMENT,
  `fnidware` int(11) DEFAULT NULL,
  `fniditem` int(11) NOT NULL,
  `ddquantity` decimal(10,0) NOT NULL,
  `dsdirection` int(11) NOT NULL COMMENT '1 = entrada, -1 = salida',
  `dsorigen` varchar(50) COLLATE utf8_bin NOT NULL,
  `dslinebase` varchar(10) COLLATE utf8_bin DEFAULT NULL,
  `dscomments` varchar(250) COLLATE utf8_bin NOT NULL,
  PRIMARY KEY (`pnid`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_bin AUTO_INCREMENT=39382 ;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `trinventorygral`
--

CREATE TABLE IF NOT EXISTS `trinventorygral` (
  `pnid` int(11) NOT NULL AUTO_INCREMENT,
  `fnidware` int(11) DEFAULT NULL,
  `fniditem` int(11) NOT NULL,
  `ddquantity` decimal(10,0) NOT NULL,
  `dscomments` varchar(250) COLLATE utf8_bin NOT NULL,
  PRIMARY KEY (`pnid`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_bin AUTO_INCREMENT=1035 ;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `trinventoryserial`
--

CREATE TABLE IF NOT EXISTS `trinventoryserial` (
  `pnid` int(11) NOT NULL AUTO_INCREMENT,
  `fnidware` int(11) DEFAULT NULL,
  `fniditem` int(11) NOT NULL,
  `dnquantity` int(11) NOT NULL DEFAULT '1',
  `dsserial` varchar(50) COLLATE utf8_bin NOT NULL,
  `fnidinventory` int(11) NOT NULL,
  PRIMARY KEY (`pnid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_bin AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `tritemware`
--

CREATE TABLE IF NOT EXISTS `tritemware` (
  `pnidtr` int(11) NOT NULL AUTO_INCREMENT,
  `fnidware` int(11) NOT NULL,
  `fniditem` int(11) NOT NULL,
  `dbactive` tinyint(1) NOT NULL DEFAULT '1',
  PRIMARY KEY (`pnidtr`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_bin COMMENT='tabla relación almacen articulo' AUTO_INCREMENT=128 ;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `trpayment`
--

CREATE TABLE IF NOT EXISTS `trpayment` (
  `pnid` int(11) NOT NULL AUTO_INCREMENT,
  `fnidsales` int(11) NOT NULL,
  `dddate` date NOT NULL,
  `dsreference` varchar(200) COLLATE utf8_bin NOT NULL,
  `dsamount` decimal(10,2) NOT NULL,
  `dscredit` decimal(10,2) NOT NULL,
  `fniduser` int(11) NOT NULL,
  `fnidstatus` int(11) DEFAULT '1' COMMENT 'estatus del pago posible cancelar',
  `dspaymentype` varchar(20) COLLATE utf8_bin DEFAULT NULL,
  PRIMARY KEY (`pnid`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_bin COMMENT='tabla de pagos' AUTO_INCREMENT=2 ;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `trsalesstatus`
--

CREATE TABLE IF NOT EXISTS `trsalesstatus` (
  `pnid` int(11) NOT NULL AUTO_INCREMENT,
  `dsreference` varchar(100) COLLATE utf8_bin DEFAULT NULL,
  `dsstatus` varchar(100) COLLATE utf8_bin DEFAULT NULL,
  `fnidsales` int(11) DEFAULT NULL,
  PRIMARY KEY (`pnid`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_bin AUTO_INCREMENT=2581 ;

--
-- Restricciones para tablas volcadas
--

--
-- Filtros para la tabla `thpurchase`
--
ALTER TABLE `thpurchase`
  ADD CONSTRAINT `fk_creator` FOREIGN KEY (`fnidcreator`) REFERENCES `tcuser` (`idtcuser`),
  ADD CONSTRAINT `fk_partner` FOREIGN KEY (`fnidpartner`) REFERENCES `tcpartner` (`pnid`);

--
-- Filtros para la tabla `thquote`
--
ALTER TABLE `thquote`
  ADD CONSTRAINT `fk_creatorsq` FOREIGN KEY (`fnidcreator`) REFERENCES `tcuser` (`idtcuser`),
  ADD CONSTRAINT `fk_partnersq` FOREIGN KEY (`fnidpartner`) REFERENCES `tcpartner` (`pnid`);

--
-- Filtros para la tabla `thsales`
--
ALTER TABLE `thsales`
  ADD CONSTRAINT `fk_creators` FOREIGN KEY (`fnidcreator`) REFERENCES `tcuser` (`idtcuser`),
  ADD CONSTRAINT `fk_partners` FOREIGN KEY (`fnidpartner`) REFERENCES `tcpartner` (`pnid`);

--
-- Filtros para la tabla `thtransferline`
--
ALTER TABLE `thtransferline`
  ADD CONSTRAINT `fk_fnidheader` FOREIGN KEY (`fnidheader`) REFERENCES `thtransfer` (`pnid`),
  ADD CONSTRAINT `fk_fniditem` FOREIGN KEY (`fniditem`) REFERENCES `tcitem` (`pnid`);

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
