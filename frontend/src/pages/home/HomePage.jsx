import { Box, Button, Container, Grid, GridItem, Heading } from "@chakra-ui/react";
import Banner from "./Banner";
import VideoCarousel from "@/components/common/carousels/VideoCarousel";
import UsersFeedbackGrid from "@/components/common/grids/UsersFeedbackGrid";
import useGetFeedback from "@/hooks/custom-hooks/useGetFeedback";
import NoFeedback from "./NoFeedback";

function HomePage() {
    const { feedbacks, loading } = useGetFeedback();

    return (
        <div style={{ background: "white" }}>
            <Banner />
            <Container mt={"50px"} spaceY={5} background={"white"}>
                {!loading && feedbacks?.length === 0 ? (
                    <NoFeedback />
                ) : (
                    <>
                        <Heading
                            color="brand.500"
                            fontFamily="Sora"
                            fontSize={30}
                        >
                            WHAT OUR USERS SAY
                        </Heading>
                        <UsersFeedbackGrid feedbacks={feedbacks} loading={loading} />
                    </>
                )}
            </Container>
        </div>
    );
}

export default HomePage;
