import { HStack, Box } from "@chakra-ui/react";
import { FaStar } from "react-icons/fa";
import PropTypes from "prop-types";

const STAR_NUMBERS = 5;

function RatingBar({ rate = 0 }) {
    return (
        <HStack>
            {Array.from({ length: STAR_NUMBERS }).map((_, index) => {
                const isFilled = index < rate;

                return (
                    <Box key={index} color={isFilled ? "yellow.500" : "gray.300"}>
                        <FaStar />
                    </Box>
                );
            })}
        </HStack>
    );
}

RatingBar.propTypes = {
    rate: PropTypes.number,
};

export default RatingBar;
