import { HStack, Box } from "@chakra-ui/react";
import { FaStar } from "react-icons/fa";
import PropTypes from "prop-types";
import { useState } from "react";

const STAR_NUMBERS = 5;

function RatingBar({ rate = 0, isReadOnly = true, onChange }) {
    const [hoverRate, setHoverRate] = useState(0);

    const handleClick = (newRate) => {
        if (!isReadOnly && onChange) {
            onChange(newRate);
        }
    };

    return (
        <HStack>
            {Array.from({ length: STAR_NUMBERS }).map((_, index) => {
                const starValue = index + 1;
                const isFilled = starValue <= (hoverRate || rate);

                return (
                    <Box 
                        key={index} 
                        color={isFilled ? "yellow.500" : "gray.300"}
                        cursor={isReadOnly ? "default" : "pointer"}
                        onClick={() => handleClick(starValue)}
                        onMouseEnter={() => !isReadOnly && setHoverRate(starValue)}
                        onMouseLeave={() => !isReadOnly && setHoverRate(0)}
                    >
                        <FaStar />
                    </Box>
                );
            })}
        </HStack>
    );
}

RatingBar.propTypes = {
    rate: PropTypes.number,
    isReadOnly: PropTypes.bool,
    onChange: PropTypes.func,
};

export default RatingBar;
