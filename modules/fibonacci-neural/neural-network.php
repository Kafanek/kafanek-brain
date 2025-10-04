<?php
/**
 * Fibonacci Brain - Neural Network Module
 * Version: 2.3.0
 * Založeno na zlatém řezu φ = 1.618033988749895
 */

if (!defined('ABSPATH')) exit;

class Fibonacci_Neural_Network {
    
    private $phi;
    private $layers = [];
    private $weights = [];
    private $biases = [];
    private $activations = [];
    private $trained = false;
    
    /**
     * Constructor
     */
    public function __construct($input_size = 10) {
        $this->phi = KOLIBRI_FIB_PHI; // 1.618033988749895
        $this->initialize_architecture($input_size);
        $this->load_trained_model();
    }
    
    /**
     * Initialize neural network architecture based on golden ratio
     */
    private function initialize_architecture($input_size) {
        // Layer sizes based on φ
        $this->layers = [
            'input' => $input_size,
            'hidden1' => round($input_size * $this->phi),           // n * φ
            'hidden2' => round($input_size * pow($this->phi, 2)),   // n * φ²
            'output' => round($input_size * pow($this->phi, 3))     // n * φ³
        ];
        
        $this->initialize_weights();
        $this->initialize_biases();
    }
    
    /**
     * Initialize weights with golden ratio scaling
     */
    private function initialize_weights() {
        $prev_size = $this->layers['input'];
        
        foreach (['hidden1', 'hidden2', 'output'] as $layer) {
            $curr_size = $this->layers[$layer];
            $this->weights[$layer] = [];
            
            for ($i = 0; $i < $curr_size; $i++) {
                $this->weights[$layer][$i] = [];
                for ($j = 0; $j < $prev_size; $j++) {
                    // Xavier initialization scaled by φ
                    $limit = sqrt(6 / ($prev_size + $curr_size)) * $this->phi;
                    $this->weights[$layer][$i][$j] = (mt_rand() / mt_getrandmax()) * 2 * $limit - $limit;
                }
            }
            
            $prev_size = $curr_size;
        }
    }
    
    /**
     * Initialize biases
     */
    private function initialize_biases() {
        foreach (['hidden1', 'hidden2', 'output'] as $layer) {
            $size = $this->layers[$layer];
            $this->biases[$layer] = array_fill(0, $size, 0);
        }
    }
    
    /**
     * Golden Ratio Sigmoid Activation
     */
    private function phi_sigmoid($x) {
        return 1 / (1 + exp(-$x * $this->phi));
    }
    
    /**
     * Golden Ratio ReLU
     */
    private function phi_relu($x) {
        return max(0, $x) * $this->phi;
    }
    
    /**
     * Fibonacci Step Function
     */
    private function fibonacci_step($x) {
        $threshold = $this->phi - 1; // 0.618
        return $x > $threshold ? 1 : 0;
    }
    
    /**
     * Forward propagation
     */
    public function predict($input) {
        if (!is_array($input) || count($input) !== $this->layers['input']) {
            return ['error' => 'Invalid input size'];
        }
        
        $activation = $input;
        $this->activations = ['input' => $activation];
        
        // Hidden Layer 1
        $activation = $this->forward_layer($activation, 'hidden1', 'phi_sigmoid');
        $this->activations['hidden1'] = $activation;
        
        // Hidden Layer 2
        $activation = $this->forward_layer($activation, 'hidden2', 'phi_relu');
        $this->activations['hidden2'] = $activation;
        
        // Output Layer
        $activation = $this->forward_layer($activation, 'output', 'phi_sigmoid');
        $this->activations['output'] = $activation;
        
        return $activation;
    }
    
    /**
     * Forward pass through one layer
     */
    private function forward_layer($input, $layer, $activation_fn) {
        $output = [];
        $size = $this->layers[$layer];
        
        for ($i = 0; $i < $size; $i++) {
            $sum = $this->biases[$layer][$i];
            
            foreach ($input as $j => $value) {
                $sum += $value * $this->weights[$layer][$i][$j];
            }
            
            // Apply activation function
            $output[] = $this->$activation_fn($sum);
        }
        
        return $output;
    }
    
    /**
     * Train network using golden ratio gradient descent
     */
    public function train($training_data, $epochs = 89) {
        $learning_rate = 1 / $this->phi; // 0.618
        
        for ($epoch = 0; $epoch < $epochs; $epoch++) {
            $total_loss = 0;
            
            foreach ($training_data as $data) {
                $input = $data['input'];
                $target = $data['output'];
                
                // Forward pass
                $output = $this->predict($input);
                
                // Calculate loss
                $loss = $this->calculate_loss($output, $target);
                $total_loss += $loss;
                
                // Backward pass (simplified)
                $this->backward_pass($target, $learning_rate);
            }
            
            // Adaptive learning rate (Fibonacci decay)
            if ($epoch % 13 == 0 && $epoch > 0) {  // Every Fibonacci number
                $learning_rate *= (1 / $this->phi);
            }
            
            // Store progress
            if ($epoch % 21 == 0) {  // Another Fibonacci number
                update_option('fibonacci_neural_training_progress', [
                    'epoch' => $epoch,
                    'loss' => $total_loss / count($training_data),
                    'learning_rate' => $learning_rate,
                    'timestamp' => current_time('mysql'),
                ]);
            }
        }
        
        $this->trained = true;
        $this->save_model();
        
        return [
            'success' => true,
            'epochs' => $epochs,
            'final_loss' => $total_loss / count($training_data),
        ];
    }
    
    /**
     * Calculate loss (MSE)
     */
    private function calculate_loss($output, $target) {
        $loss = 0;
        foreach ($output as $i => $value) {
            $error = isset($target[$i]) ? ($target[$i] - $value) : 0;
            $loss += $error * $error;
        }
        return $loss / count($output);
    }
    
    /**
     * Backward pass (simplified gradient descent)
     */
    private function backward_pass($target, $learning_rate) {
        // Simplified backpropagation with φ scaling
        $output_error = [];
        foreach ($this->activations['output'] as $i => $value) {
            $target_val = isset($target[$i]) ? $target[$i] : 0;
            $output_error[$i] = ($target_val - $value) * $this->phi;
        }
        
        // Update output layer weights
        foreach ($this->weights['output'] as $i => &$neuron_weights) {
            foreach ($neuron_weights as $j => &$weight) {
                $gradient = $output_error[$i] * $this->activations['hidden2'][$j];
                $weight += $learning_rate * $gradient;
            }
            
            // Update bias
            $this->biases['output'][$i] += $learning_rate * $output_error[$i];
        }
    }
    
    /**
     * Save trained model
     */
    private function save_model() {
        $model_data = [
            'layers' => $this->layers,
            'weights' => $this->weights,
            'biases' => $this->biases,
            'trained' => $this->trained,
            'phi' => $this->phi,
            'timestamp' => current_time('mysql'),
        ];
        
        update_option('fibonacci_neural_model', $model_data);
    }
    
    /**
     * Load trained model
     */
    private function load_trained_model() {
        $model_data = get_option('fibonacci_neural_model');
        
        if ($model_data && isset($model_data['trained']) && $model_data['trained']) {
            $this->layers = $model_data['layers'];
            $this->weights = $model_data['weights'];
            $this->biases = $model_data['biases'];
            $this->trained = $model_data['trained'];
        }
    }
    
    /**
     * Get model status
     */
    public function get_status() {
        return [
            'architecture' => $this->layers,
            'trained' => $this->trained,
            'phi' => $this->phi,
            'total_params' => $this->count_parameters(),
            'model_size_kb' => strlen(serialize($this->weights)) / 1024,
        ];
    }
    
    /**
     * Count total parameters
     */
    private function count_parameters() {
        $total = 0;
        foreach ($this->weights as $layer => $neurons) {
            foreach ($neurons as $neuron) {
                $total += count($neuron);
            }
        }
        $total += array_sum(array_map('count', $this->biases));
        return $total;
    }
    
    /**
     * Reset network
     */
    public function reset() {
        $this->initialize_weights();
        $this->initialize_biases();
        $this->trained = false;
        delete_option('fibonacci_neural_model');
        delete_option('fibonacci_neural_training_progress');
    }
}
