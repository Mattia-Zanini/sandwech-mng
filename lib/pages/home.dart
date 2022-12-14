import 'package:flutter/material.dart';

class Home extends StatefulWidget {
  const Home({super.key});

  @override
  State<Home> createState() => _ProfilePageState();
}

class _ProfilePageState extends State<Home> {
  @override
  Widget build(BuildContext context) {
    return const Center(
      child: Text("pere home"),
    );
  }
}
