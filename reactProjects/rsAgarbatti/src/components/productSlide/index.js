import React from "react";
import Slider from "react-slick";
import "slick-carousel/slick/slick.css";
import "slick-carousel/slick/slick-theme.css";
import agarbattiArr from "./productData.js";
import style from "./index.module.css";
import Arrows from "./components/arrows.js";

const ProductSlide = () => {
  const sliderRef = React.useRef();

  var settings = {
    dots: true,
    infinite: true,
    speed: 500,
    slidesToShow: 1,
    slidesToScroll: 1,
    autoplay: true,
    autoplaySpeed: 4000,
    arrows: true,
    nextArrow: <Arrows direction="right" clickFunction={sliderRef} />,
    prevArrow: <Arrows direction="left" clickFunction={sliderRef} />,

    responsive: [
      {
        breakpoint: 600,
        settings: {
          dots: false,
        },
      },
    ],
  };

  return (
    <>
      <Slider {...settings} ref={sliderRef}>
        {agarbattiArr.map((v, i) => {
          return (
            <div key={i} className={`${style.productDiv}`}>
              <img
                key={i}
                src={v.img}
                alt={v.name}
                className={`${style.productImg}`}
              />
            </div>
          );
        })}
      </Slider>
    </>
  );
};

export default ProductSlide;
