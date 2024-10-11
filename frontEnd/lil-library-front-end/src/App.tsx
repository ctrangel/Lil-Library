
import './App.css'
import { Box, Button, Heading} from '@chakra-ui/react'

function App() {

  return (
    <Box textAlign="center" py={10}>
      <Heading>Welcome to the Book Library</Heading>
      <Button mt={5} colorScheme="teal">
        Get Started
      </Button>
    </Box>
  );
}

export default App
