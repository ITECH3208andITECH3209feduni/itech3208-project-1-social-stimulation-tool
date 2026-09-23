import { Container, VStack } from "@chakra-ui/react";
import AboutUs from "./AboutUs";
import OurMission from "./OurMission";
import OurTeam from "./OurTeam";
import Contribution from "./Contribution";
import Crowdfunding from "./Crowdfunding";
import WINNews from "./WINNews";

function AboutPage() {
    return (
        <Container maxW="1200px" px={{ base: 5, md: 8, lg: 10 }} py={12}>
            <VStack gap={{ base: 12, md: 16 }} align="stretch">
                <AboutUs />
                <OurMission />
                <OurTeam />
                <Contribution />
                <WINNews/>
                <Crowdfunding />
            </VStack>
        </Container>
    );
}

export default AboutPage;
